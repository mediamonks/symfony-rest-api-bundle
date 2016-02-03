<?php

namespace MediaMonks\RestApiBundle\EventSubscriber;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use MediaMonks\RestApiBundle\Response\Response as RestApiResponse;
use MediaMonks\RestApiBundle\Response\JsonResponse;
use MediaMonks\RestApiBundle\Model\ResponseContainer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse as SymfonyJsonResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class IOEventSubscriber implements EventSubscriberInterface
{
    const FORMAT_JSON = 'json';
    const FORMAT_XML = 'xml';

    const WRAPPER_PADDING = 'padding';
    const WRAPPER_POST_MESSAGE = 'postMessage';

    /**
     * @var array
     */
    protected $formats = [self::FORMAT_JSON, self::FORMAT_XML];

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var boolean
     */
    protected $active;

    /**
     * @var string
     */
    protected $origin;

    /**
     * @var array
     */
    protected $whitelist = [
        '~^/api~'
    ];

    /**
     * @var array
     */
    protected $blacklist = [
        '~^/api/doc~'
    ];

    /**
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
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST    => [
                ['onRequest', 512]
            ],
            KernelEvents::EXCEPTION  => [
                ['onException', 512],
            ],
            KernelEvents::VIEW       => [
                ['onView', 0],
            ],
            KernelEvents::RESPONSE   => [
                ['onResponse', 0],
                ['onResponseLate', -512],
            ]
        ];
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options)
    {
        if (isset($options['origin'])) {
            $this->setOrigin($options['origin']);
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * @param string $origin
     * @return $this
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;
        return $this;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onRequest(GetResponseEvent $event)
    {
        if (!$this->isActive($event)) {
            return;
        }

        $this->detectRequestFormat($event->getRequest());
        $this->acceptJsonBody($event->getRequest());
    }

    /**
     * @param Request $request
     */
    protected function acceptJsonBody(Request $request)
    {
        if (strpos($request->headers->get('Content-Type'), 'application/json') === 0) {
            $data = json_decode($request->getContent(), true);
            $request->request->replace(is_array($data) ? $data : []);
        }
    }

    /**
     * @param Request $request
     */
    protected function detectRequestFormat(Request $request)
    {
        $default = self::FORMAT_JSON;
        $format  = $request->getRequestFormat($request->query->get('_format', $default));
        if (!in_array($format, $this->formats)) {
            $format = $default;
        }
        $request->setRequestFormat($format);
    }

    /**
     * convert exception to rest api response
     *
     * @param GetResponseForExceptionEvent $event
     */
    public function onException(GetResponseForExceptionEvent $event)
    {
        if (!$this->isActive($event)) {
            return;
        }
        $event->setResponse(
            new RestApiResponse(ResponseContainer::createAutoDetect($event->getException()))
        );
    }

    /**
     * convert response to rest api response
     *
     * @param GetResponseForControllerResultEvent $event
     */
    public function onView(GetResponseForControllerResultEvent $event)
    {
        if (!$this->isActive($event)) {
            return;
        }
        $response = new RestApiResponse(
            ResponseContainer::createAutoDetect($event->getControllerResult())
        );
        $event->setResponse($response);
    }

    /**
     * converts content to correct output format
     *
     * @param FilterResponseEvent $event
     */
    public function onResponse(FilterResponseEvent $event)
    {
        if (!$this->isActive($event)) {
            return;
        }

        $request  = $event->getRequest();
        $response = $event->getResponse();
        $content  = $response->getContent();

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
            || ($request->getRequestFormat() == self::FORMAT_JSON && $request->query->has('callback'))
        ) {
            $content->setReturnStatusCode(true);
            $response->setStatusCode(Response::HTTP_OK);
            $response->headers->set('X-Status-Code', Response::HTTP_OK);
        }

        // serialize content container
        try {
            $context = new SerializationContext();
            $context->setSerializeNull(true);
            $format            = $event->getRequest()->getRequestFormat();
            $contentSerialized = $this->serializer->serialize($content->toArray(), $format, $context);
            switch ($format) {
                case self::FORMAT_XML:
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

        $event->setResponse($response);
    }

    /**
     * wrap the content if needed
     *
     * @param FilterResponseEvent $event
     */
    public function onResponseLate(FilterResponseEvent $event)
    {
        if (!$this->isActive($event)) {
            return;
        }
        $request  = $event->getRequest();
        $response = $event->getResponse();
        if ($request->getRequestFormat() === self::FORMAT_JSON
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
                                'origin'   => $this->getOrigin()
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

    /**
     * @param KernelEvent $event
     * @return bool
     */
    protected function isActive(KernelEvent $event)
    {
        $request = $event->getRequest();

        if ($this->active === true) {
            return true;
        }

        $this->active = false;

        foreach ($this->whitelist as $whitelist) {
            if (preg_match($whitelist, $request->getPathInfo())) {
                return $this->active = true;
            }
        }

        foreach ($this->blacklist as $blacklist) {
            if (preg_match($blacklist, $request->getPathInfo())) {
                $this->active = false;
            }
        }

        return $this->active;
    }
}
