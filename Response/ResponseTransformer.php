<?php

namespace MediaMonks\RestApiBundle\Response;

use MediaMonks\RestApiBundle\Model\ResponseModel;
use MediaMonks\RestApiBundle\Model\ResponseModelFactory;
use MediaMonks\RestApiBundle\Request\Format;
use MediaMonks\RestApiBundle\Serializer\SerializerInterface;
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
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * @var string
     */
    protected $postMessageOrigin;

    /**
     * @var ResponseModelFactory
     */
    protected $responseModelFactory;

    /**
     * @var bool
     */
    protected $wrapResponseData = true;

    /**
     * ResponseTransformer constructor.
     * @param SerializerInterface $serializer
     * @param array $options
     */
    public function __construct(SerializerInterface $serializer, $options = [])
    {
        $this->serializer = $serializer;
        $this->setOptions($options);
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        if (isset($options['debug'])) {
            $this->setDebug($options['debug']);
        }
        if (isset($options['post_message_origin'])) {
            $this->setPostMessageOrigin($options['post_message_origin']);
        }
        if (isset($options['wrap_response_data'])) {
            $this->setWrapResponseData($options['wrap_response_data']);
        }
    }

    /**
     * @return boolean
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * @param boolean $debug
     * @return ResponseTransformer
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;

        return $this;
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
     * @return bool
     */
    public function isWrapResponseData()
    {
        return $this->wrapResponseData;
    }

    /**
     * @param bool $wrapResponseData
     */
    public function setWrapResponseData($wrapResponseData)
    {
        $this->wrapResponseData = $wrapResponseData;
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
            $responseModel = $this->getResponseModelFactory()->createFromContent($response);
        }

        $responseModel->setReturnStackTrace($this->isDebug());
        $response->setStatusCode($responseModel->getStatusCode());
        $this->forceStatusCodeHttpOK($request, $response, $responseModel);
        $response = $this->createSerializedResponse($request, $response, $responseModel);

        return $response;
    }

    /**
     * @param ResponseModelFactory $factory
     */
    public function setResponseModelFactory($factory)
    {
        $this->responseModelFactory = $factory;
    }

    /**
     * @return ResponseModelFactory
     */
    public function getResponseModelFactory()
    {
        if (!isset($this->responseModelFactory)) {
            $this->responseModelFactory = ResponseModelFactory::createFactory();
        }

        return $this->responseModelFactory;
    }

    /**
     * @param Request $request
     * @param SymfonyResponse $response
     */
    public function transformLate(Request $request, SymfonyResponse $response)
    {
        if ($request->getRequestFormat() === Format::FORMAT_JSON
            && $request->query->has(self::PARAMETER_CALLBACK)
            && $response instanceof JsonResponse
        ) {
            $this->wrapResponse($request, $response);
        }
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
    protected function createSerializedResponse(
        Request $request,
        SymfonyResponse $response,
        ResponseModel $responseModel
    ) {
        try {
            $response = $this->serialize($request, $response, $responseModel);
        } catch (\Exception $e) {
            $response = new SymfonyJsonResponse(
                [
                    'error' => [
                        'code'    => Error::CODE_SERIALIZE,
                        'message' => $e->getMessage(),
                    ],
                ]
            );
        }

        return $response;
    }

    /**
     * @param Request $request
     * @param SymfonyResponse $response
     * @param ResponseModel $responseModel
     * @return JsonResponse|SymfonyResponse
     */
    protected function serialize(Request $request, SymfonyResponse $response, ResponseModel $responseModel)
    {
        switch ($request->getRequestFormat()) {
            case Format::FORMAT_XML:
                $response->setContent($this->getSerializedContent($request, $responseModel));
                break;
            default:
                $headers = $response->headers;
                $response = new JsonResponse(
                    $this->getSerializedContent($request, $responseModel),
                    $response->getStatusCode()
                );
                $response->headers = $headers; // some headers might mess up if we pass it to the JsonResponse
                break;
        }

        return $response;
    }

    /**
     * @param Request $request
     * @param ResponseModel $responseModel
     * @return mixed|string
     */
    protected function getSerializedContent(Request $request, ResponseModel $responseModel)
    {
        return $this->serializer->serialize($responseModel->toArray($this->isWrapResponseData()), $request->getRequestFormat());
    }

    /**
     * @param Request $request
     * @param JsonResponse $response
     * @throws \Exception
     */
    protected function wrapResponse(Request $request, JsonResponse $response)
    {
        switch ($request->query->get(self::PARAMETER_WRAPPER)) {
            case self::WRAPPER_POST_MESSAGE:
                $response->setContent(
                    sprintf(
                        $this->getPostMessageTemplate(),
                        $response->getContent(),
                        $this->getCallbackFromRequest($request),
                        $this->getPostMessageOrigin()
                    )
                )->headers->set('Content-Type', 'text/html');
                break;
            default:
                $response->setCallback($request->query->get(self::PARAMETER_CALLBACK));
                break;
        }
    }

    /**
     * @param Request $request
     * @return string
     */
    protected function getCallbackFromRequest(Request $request)
    {
        $response = new JsonResponse('');
        $response->setCallback($request->query->get(self::PARAMETER_CALLBACK));

        return $response->getCallback();
    }

    /**
     * @return string
     */
    protected function getPostMessageTemplate()
    {
        return <<<EOD
<html>
<body>
<script>
    try {
        var data = %s;
    }
    catch (error) {
        var data = {"error": {"code": "error.parse.post_message", "message": "Post message parse error"}};
    }

    top.postMessage(JSON.stringify({
        name: '%s',
        result: data
    }), '%s');
</script>
</body>
</html>
EOD;
    }
}
