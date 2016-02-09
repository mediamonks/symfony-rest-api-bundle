<?php

namespace MediaMonks\RestApiBundle\Response;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use MediaMonks\RestApiBundle\Model\ResponseModel;
use MediaMonks\RestApiBundle\Request\Format;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpFoundation\JsonResponse as SymfonyJsonResponse;

class ResponseTransformer implements ResponseTransformerInterface
{
    const WRAPPER_PADDING = 'padding';
    const WRAPPER_POST_MESSAGE = 'postMessage';

    const PARAMETER_CALLBACK = 'callback';
    const PARAMETER_WRAPPER = '_wrapper';

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var string
     */
    protected $postMessageOrigin;

    /**
     * ResponseTransformer constructor.
     * @param Serializer $serializer
     * @param \Twig_Environment $twig
     * @param array $options
     */
    public function __construct(Serializer $serializer, \Twig_Environment $twig, $options = [])
    {
        $this->serializer = $serializer;
        $this->twig       = $twig;
        $this->setOptions($options);
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        if (isset($options['post_message_origin'])) {
            $this->setPostMessageOrigin($options['post_message_origin']);
        }
    }

    /**
     * @return string
     */
    public function getPostMessageOrigin()
    {
        return $this->postMessageOrigin;
    }

    /**
     * @param string $postMessageOrigin
     * @return ResponseTransformer
     */
    public function setPostMessageOrigin($postMessageOrigin)
    {
        $this->postMessageOrigin = $postMessageOrigin;
        return $this;
    }

    /**
     * @param Request $request
     * @param SymfonyResponse $response
     * @return SymfonyResponse
     */
    public function transformEarly(Request $request, SymfonyResponse $response)
    {
        $responseModel = $response->getContent();

        if (!$responseModel instanceof ResponseModel) {
            $responseModel = ResponseModel::createAutoDetect($response);
        }

        $response->setStatusCode($responseModel->getStatusCode());
        $this->forceStatusCodeHttpOK($request, $response, $responseModel);
        $response = $this->serialize($request, $response, $responseModel);

        return $response;
    }

    /**
     * Check if we should put the status code in the output and force a 200 OK in the header
     *
     * @param Request $request
     * @param SymfonyResponse $response
     * @param ResponseModel $responseModel
     */
    protected function forceStatusCodeHttpOK(
        Request $request,
        SymfonyResponse $response,
        ResponseModel $responseModel
    ) {
        if ($request->headers->has('X-Force-Status-Code-200')
            || ($request->getRequestFormat() == Format::FORMAT_JSON && $request->query->has(self::PARAMETER_CALLBACK))
        ) {
            $responseModel->setReturnStatusCode(true);
            $response->setStatusCode(Response::HTTP_OK);
            $response->headers->set('X-Status-Code', Response::HTTP_OK);
        }
    }

    /**
     * @param Request $request
     * @param SymfonyResponse $response
     * @param ResponseModel $responseModel
     * @return SymfonyResponse
     */
    protected function serialize(Request $request, SymfonyResponse $response, ResponseModel $responseModel)
    {
        try {
            $context = new SerializationContext();
            $context->setSerializeNull(true);
            $format            = $request->getRequestFormat();
            $contentSerialized = $this->serializer->serialize($responseModel->toArray(), $format, $context);
            switch ($format) {
                case Format::FORMAT_XML:
                    $response->setContent($contentSerialized);
                    break;
                default:
                    $headers           = $response->headers;
                    $response          = new JsonResponse($contentSerialized, $response->getStatusCode());
                    $response->headers = $headers; // some headers might mess up if we pass it to the JsonResponse
                    break;
            }
        } catch (\Exception $e) {
            $response = new SymfonyJsonResponse([
                'error' => [
                    'code'    => ResponseModel::ERROR_CODE_REST_API_BUNDLE,
                    'message' => $e->getMessage()
                ]
            ]);
        }

        return $response;
    }

    /**
     * @param Request $request
     * @param SymfonyResponse $response
     */
    public function transformLate(Request $request, SymfonyResponse $response)
    {
        $this->wrapResponse($request, $response);
    }

    /**
     * @param Request $request
     * @param SymfonyResponse $response
     */
    protected function wrapResponse(Request $request, SymfonyResponse $response)
    {
        if ($request->getRequestFormat() === Format::FORMAT_JSON
            && $request->query->has(self::PARAMETER_CALLBACK)
            && $response instanceof JsonResponse
        ) {
            switch ($request->query->get(self::PARAMETER_WRAPPER)) {
                case self::WRAPPER_POST_MESSAGE:
                    $response->setContent(
                        $this->twig->render(
                            'MediaMonksRestApiBundle::post_message.html.twig',
                            [
                                'request'  => $request,
                                'response' => $response,
                                'callback' => $request->query->get(self::PARAMETER_CALLBACK),
                                'origin'   => $this->getPostMessageOrigin()
                            ]
                        )
                    )->headers->set('Content-Type', 'text/html');
                    break;
                default:
                    $response->setCallback($request->query->get(self::PARAMETER_CALLBACK));
                    break;
            }
        }
    }
}
