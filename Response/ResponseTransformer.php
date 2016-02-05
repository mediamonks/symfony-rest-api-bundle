<?php

namespace MediaMonks\RestApiBundle\Response;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use MediaMonks\RestApiBundle\Model\ResponseContainer;
use MediaMonks\RestApiBundle\Request\Format;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpFoundation\JsonResponse as SymfonyJsonResponse;

class ResponseTransformer implements ResponseTransformerInterface
{
    const WRAPPER_PADDING = 'padding';
    const WRAPPER_POST_MESSAGE = 'postMessage';

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var
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
        if(isset($options['post_message_origin'])) {
            $this->setPostMessageOrigin($options['post_message_origin']);
        }
    }

    /**
     * @return mixed
     */
    public function getPostMessageOrigin()
    {
        return $this->postMessageOrigin;
    }

    /**
     * @param mixed $postMessageOrigin
     * @return ResponseTransformer
     */
    public function setPostMessageOrigin($postMessageOrigin)
    {
        $this->postMessageOrigin = $postMessageOrigin;
        return $this;
    }

    /**
     * @param SymfonyResponse $response
     * @return SymfonyResponse
     */
    public function transformEarly(Request $request, SymfonyResponse $response)
    {
        $content = $response->getContent();

        // convert content to content container
        if (!$content instanceof ResponseContainer) {
            $content = ResponseContainer::createAutoDetect($response);
        }

        // override http status code if needed
        $statusCode = $content->getStatusCode();
        if (!empty($statusCode)) {
            $response->setStatusCode($content->getStatusCode());
        }

        // set 204 header for empty content
        if ($content->isEmpty() && !$response->isRedirect()) {
            $response->setStatusCode(Response::HTTP_NO_CONTENT);
            $content->setStatusCode(Response::HTTP_NO_CONTENT);
        }

        // put statusCode in response and force 200 OK in header?
        if ($request->headers->has('X-Force-Status-Code-200')
            || ($request->getRequestFormat() == Format::FORMAT_JSON && $request->query->has('callback'))
        ) {
            $content->setReturnStatusCode(true);
            $response->setStatusCode(Response::HTTP_OK);
            $response->headers->set('X-Status-Code', Response::HTTP_OK);
        }

        // serialize content container
        try {
            $context = new SerializationContext();
            $context->setSerializeNull(true);
            $format            = $request->getRequestFormat();
            $contentSerialized = $this->serializer->serialize($content->toArray(), $format, $context);
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
                    'code'    => ResponseContainer::ERROR_CODE_REST_API_BUNDLE,
                    'message' => $e->getMessage()
                ]
            ]);
        }

        // force empty output on a no-content response
        if ($content->isEmpty() && $response->isEmpty()) {
            $response->setContent('');
        }

        return $response;
    }

    public function transformLate(Request $request, SymfonyResponse $response)
    {
        if ($request->getRequestFormat() === Format::FORMAT_JSON
            && $request->query->has('callback')
            && $response instanceof JsonResponse
        ) {
            switch ($request->query->get('_wrapper')) {
                case self::WRAPPER_POST_MESSAGE:
                    $response->setContent(
                        $this->twig->render(
                            'MediaMonksRestApiBundle::post_message.html.twig',
                            [
                                'request'  => $request,
                                'response' => $response,
                                'callback' => $request->query->get('callback'),
                                'origin'   => $this->getPostMessageOrigin()
                            ]
                        )
                    )->headers->set('Content-Type', 'text/html');
                    break;
                default:
                    $response->setCallback($request->query->get('callback'));
                    break;
            }
        }
    }
}
