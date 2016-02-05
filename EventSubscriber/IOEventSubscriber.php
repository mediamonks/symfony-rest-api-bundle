<?php

namespace MediaMonks\RestApiBundle\EventSubscriber;

use MediaMonks\RestApiBundle\Request\RequestMatcher;
use MediaMonks\RestApiBundle\Request\RequestTransformer;
use MediaMonks\RestApiBundle\Response\Response as RestApiResponse;
use MediaMonks\RestApiBundle\Model\ResponseContainer;
use MediaMonks\RestApiBundle\Response\ResponseTransformer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class IOEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var RequestMatcher
     */
    protected $requestMatcher;

    /**
     * @var RequestTransformer
     */
    protected $requestTransformer;

    /**
     * @var ResponseTransformer
     */
    protected $responseTransformer;

    /**
     * IOEventSubscriber constructor.
     * @param RequestMatcher $requestMatcher
     * @param RequestTransformer $requestTransformer
     * @param ResponseTransformer $responseTransformer
     */
    public function __construct(
        RequestMatcher $requestMatcher,
        RequestTransformer $requestTransformer,
        ResponseTransformer $responseTransformer
    ) {
        $this->requestMatcher      = $requestMatcher;
        $this->requestTransformer  = $requestTransformer;
        $this->responseTransformer = $responseTransformer;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST   => [
                ['onRequest', 512]
            ],
            KernelEvents::EXCEPTION => [
                ['onException', 512],
            ],
            KernelEvents::VIEW      => [
                ['onView', 0],
            ],
            KernelEvents::RESPONSE  => [
                ['onResponse', 0],
                ['onResponseLate', -512],
            ]
        ];
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onRequest(GetResponseEvent $event)
    {
        if (!$this->requestMatcher->matches($event->getRequest())) {
            return;
        }
        $this->requestTransformer->transform($event->getRequest());
    }

    /**
     * convert exception to rest api response
     *
     * @param GetResponseForExceptionEvent $event
     */
    public function onException(GetResponseForExceptionEvent $event)
    {
        if (!$this->requestMatcher->matches($event->getRequest())) {
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
        if (!$this->requestMatcher->matches($event->getRequest())) {
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
        if (!$this->requestMatcher->matches($event->getRequest())) {
            return;
        }
        $event->setResponse($this->responseTransformer->transformEarly($event->getRequest(), $event->getResponse()));
    }

    /**
     * wrap the content if needed
     *
     * @param FilterResponseEvent $event
     */
    public function onResponseLate(FilterResponseEvent $event)
    {
        if (!$this->requestMatcher->matches($event->getRequest())) {
            return;
        }
        $this->responseTransformer->transformLate($event->getRequest(), $event->getResponse());
    }
}
