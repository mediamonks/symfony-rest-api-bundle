<?php

namespace MediaMonks\RestApiBundle\EventSubscriber;

use MediaMonks\RestApiBundle\Model\ResponseModelFactory;
use MediaMonks\RestApiBundle\Request\RequestMatcherInterface;
use MediaMonks\RestApiBundle\Request\RequestTransformerInterface;
use MediaMonks\RestApiBundle\Response\Response as RestApiResponse;
use MediaMonks\RestApiBundle\Response\ResponseTransformerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class IOEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var RequestMatcherInterface
     */
    protected $requestMatcher;

    /**
     * @var RequestTransformerInterface
     */
    protected $requestTransformer;

    /**
     * @var ResponseTransformerInterface
     */
    protected $responseTransformer;

    /**
     * IOEventSubscriber constructor.
     * @param RequestMatcherInterface $requestMatcher
     * @param RequestTransformerInterface $requestTransformer
     * @param ResponseTransformerInterface $responseTransformer
     */
    public function __construct(
        RequestMatcherInterface $requestMatcher,
        RequestTransformerInterface $requestTransformer,
        ResponseTransformerInterface $responseTransformer
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
                ['onResponseEarly', 0],
                ['onResponseLate', -512],
            ]
        ];
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onRequest(GetResponseEvent $event)
    {
        if (!$this->eventRequestMatches($event)) {
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
        if (!$this->eventRequestMatches($event)) {
            return;
        }
        $event->setResponse($this->createRestApiResponse($event->getException()));
    }

    /**
     * convert response to rest api response
     *
     * @param GetResponseForControllerResultEvent $event
     */
    public function onView(GetResponseForControllerResultEvent $event)
    {
        if (!$this->eventRequestMatches($event)) {
            return;
        }
        $event->setResponse($this->createRestApiResponse($event->getControllerResult()));
    }

    /**
     * converts content to correct output format
     *
     * @param FilterResponseEvent $event
     */
    public function onResponseEarly(FilterResponseEvent $event)
    {
        if (!$this->eventRequestMatches($event)) {
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
        if (!$this->eventRequestMatches($event)) {
            return;
        }
        $this->responseTransformer->transformLate($event->getRequest(), $event->getResponse());
    }

    /**
     * @param KernelEvent $event
     * @return bool
     */
    protected function eventRequestMatches(KernelEvent $event)
    {
        return $this->requestMatcher->matches($event->getRequest(), $event->getRequestType());
    }

    /**
     * @param $data
     * @return RestApiResponse
     */
    protected function createRestApiResponse($data)
    {
        return new RestApiResponse(ResponseModelFactory::createFactory()->createFromContent($data));
    }
}
