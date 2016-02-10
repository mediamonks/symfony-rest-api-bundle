<?php

namespace MediaMonks\RestApiBundle\Tests\EventSubscriber;


use MediaMonks\RestApiBundle\EventSubscriber\IOEventSubscriber;
use MediaMonks\RestApiBundle\Request\RequestMatcherInterface;
use \Mockery as m;
use Symfony\Component\HttpKernel\KernelEvents;

class IOEventSubscriberTest extends \PHPUnit_Framework_TestCase
{
    protected function getSubject($mocks = null)
    {
        list($matcher, $requestTransformer, $responseTransformer) = $mocks ?: $this->getMocks();
        return new IOEventSubscriber($matcher, $requestTransformer, $responseTransformer);
    }
    protected function getMocks()
    {
        $matcher = m::mock('MediaMonks\RestApiBundle\Request\RequestMatcherInterface');
        $requestTransformer = m::mock('MediaMonks\RestApiBundle\Request\RequestTransformerInterface');
        $responseTransformer = m::mock('MediaMonks\RestApiBundle\Response\ResponseTransformerInterface');

        return [$matcher, $requestTransformer, $responseTransformer];
    }

    public function testGetSubscribedEvents()
    {
        $subscribedEvents = IOEventSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(KernelEvents::REQUEST, $subscribedEvents);
        $this->assertArrayHasKey(KernelEvents::EXCEPTION, $subscribedEvents);
        $this->assertArrayHasKey(KernelEvents::VIEW, $subscribedEvents);
        $this->assertArrayHasKey(KernelEvents::RESPONSE, $subscribedEvents);
    }

    public function testOnRequestIsBound()
    {
        $this->methodIsBound('onRequest', KernelEvents::REQUEST);
    }
    public function testOnRequestNoMatch()
    {
        list($matcher, $requestTransformer, $responseTransformer) = $this->getMocks();
        $matcher->shouldReceive('matches')->andReturn(false);
        $requestTransformer->shouldReceive('transform');

        $subject = $this->getSubject([$matcher, $requestTransformer, $responseTransformer]);

        $mockEvent = m::mock('Symfony\Component\HttpKernel\Event\GetResponseEvent');
        $mockEvent->shouldReceive('getRequest')->andReturn(m::mock('Symfony\Component\HttpFoundation\Request'));
        $mockEvent->shouldReceive('getRequestType');

        $subject->onRequest($mockEvent);

        try {
            $requestTransformer->shouldNotHaveReceived('transform');
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }
    public function testOnRequest()
    {
        list($matcher, $requestTransformer, $responseTransformer) = $this->getMocks();
        $matcher->shouldReceive('matches')->andReturn(true);
        $requestTransformer->shouldReceive('transform');

        $subject = $this->getSubject([$matcher, $requestTransformer, $responseTransformer]);

        $mockEvent = m::mock('Symfony\Component\HttpKernel\Event\GetResponseEvent');
        $mockEvent->shouldReceive('getRequest')->andReturn(m::mock('Symfony\Component\HttpFoundation\Request'));
        $mockEvent->shouldReceive('getRequestType');

        $subject->onRequest($mockEvent);

        try {
            $requestTransformer->shouldHaveReceived('transform')->between(1, 1);
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }

    public function testOnExceptionIsBound()
    {
        $this->methodIsBound('onException', KernelEvents::EXCEPTION);
    }
    public function testOnExceptionNoMatch()
    {
        list($matcher, $requestTransformer, $responseTransformer) = $this->getMocks();
        $matcher->shouldReceive('matches')->andReturn(false);

        $subject = $this->getSubject([$matcher, $requestTransformer, $responseTransformer]);

        $mockEvent = m::mock('Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent');
        $mockEvent->shouldReceive('getRequest')->andReturn(m::mock('Symfony\Component\HttpFoundation\Request'));
        $mockEvent->shouldReceive('getRequestType');

        $subject->onException($mockEvent);

        try {
            $mockEvent->shouldNotHaveReceived('setResponse');
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }
    public function testOnException()
    {
        list($matcher, $requestTransformer, $responseTransformer) = $this->getMocks();
        $matcher->shouldReceive('matches')->andReturn(true);

        $subject = $this->getSubject([$matcher, $requestTransformer, $responseTransformer]);

        $mockEvent = m::mock('Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent');
        $mockEvent->shouldReceive('getRequest')->andReturn(m::mock('Symfony\Component\HttpFoundation\Request'));
        $mockEvent->shouldReceive('getRequestType');
        $mockEvent->shouldReceive('setResponse');
        $mockEvent->shouldReceive('getException');

        $subject->onException($mockEvent);

        try {
            $mockEvent->shouldHaveReceived('setResponse')->between(1, 1);
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }

    public function testOnViewIsBould()
    {
        $this->methodIsBound('onView', KernelEvents::VIEW);
    }
    public function testOnViewNoMatch()
    {
        list($matcher, $requestTransformer, $responseTransformer) = $this->getMocks();
        $matcher->shouldReceive('matches')->andReturn(false);

        $subject = $this->getSubject([$matcher, $requestTransformer, $responseTransformer]);

        $mockEvent = m::mock('Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent');
        $mockEvent->shouldReceive('getRequest')->andReturn(m::mock('Symfony\Component\HttpFoundation\Request'));
        $mockEvent->shouldReceive('getRequestType');

        $subject->onView($mockEvent);

        try {
            $mockEvent->shouldNotHaveReceived('setResponse');
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }
    public function testOnView()
    {
        list($matcher, $requestTransformer, $responseTransformer) = $this->getMocks();
        $matcher->shouldReceive('matches')->andReturn(true);

        $subject = $this->getSubject([$matcher, $requestTransformer, $responseTransformer]);

        $mockEvent = m::mock('Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent');
        $mockEvent->shouldReceive('getRequest')->andReturn(m::mock('Symfony\Component\HttpFoundation\Request'));
        $mockEvent->shouldReceive('getRequestType');
        $mockEvent->shouldReceive('getControllerResult');
        $mockEvent->shouldReceive('setResponse');

        $subject->onView($mockEvent);

        try {
            $mockEvent->shouldHaveReceived('setResponse')->between(1, 1);
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }

    public function testOnResponseEarlyIsBound()
    {
        $this->methodIsBound('onResponseEarly', KernelEvents::RESPONSE);
    }
    public function testOnResponseEarlyNoMatch()
    {
        list($matcher, $requestTransformer, $responseTransformer) = $this->getMocks();
        $matcher->shouldReceive('matches')->andReturn(false);

        $subject = $this->getSubject([$matcher, $requestTransformer, $responseTransformer]);

        $mockEvent = m::mock('Symfony\Component\HttpKernel\Event\FilterResponseEvent');
        $mockEvent->shouldReceive('getRequest')->andReturn(m::mock('Symfony\Component\HttpFoundation\Request'));
        $mockEvent->shouldReceive('getRequestType');

        $subject->onResponseEarly($mockEvent);

        try {
            $mockEvent->shouldNotHaveReceived('setResponse');
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }
    public function testOnResponseEarly()
    {
        list($matcher, $requestTransformer, $responseTransformer) = $this->getMocks();
        $matcher->shouldReceive('matches')->andReturn(true);
        $responseTransformer->shouldReceive('transformEarly')->andReturn(m::mock('Symfony\Component\HttpFoundation\Response'));

        $subject = $this->getSubject([$matcher, $requestTransformer, $responseTransformer]);

        $mockEvent = m::mock('Symfony\Component\HttpKernel\Event\FilterResponseEvent');
        $mockEvent->shouldReceive('getRequest')->andReturn(m::mock('Symfony\Component\HttpFoundation\Request'));
        $mockEvent->shouldReceive('getRequestType');
        $mockEvent->shouldReceive('getResponse')->andReturn(m::mock('Symfony\Component\HttpFoundation\Response'));
        $mockEvent->shouldReceive('setResponse');

        $subject->onResponseEarly($mockEvent);

        try {
            $mockEvent->shouldHaveReceived('setResponse')->between(1, 1);
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }

    public function testOnResponseLateIsBound()
    {
        $this->methodIsBound('onResponseLate', KernelEvents::RESPONSE);
    }
    public function testOnResponseLateNoMatch()
    {
        list($matcher, $requestTransformer, $responseTransformer) = $this->getMocks();
        $matcher->shouldReceive('matches')->andReturn(false);

        $subject = $this->getSubject([$matcher, $requestTransformer, $responseTransformer]);

        $mockEvent = m::mock('Symfony\Component\HttpKernel\Event\FilterResponseEvent');
        $mockEvent->shouldReceive('getRequest')->andReturn(m::mock('Symfony\Component\HttpFoundation\Request'));
        $mockEvent->shouldReceive('getRequestType');

        $subject->onResponseLate($mockEvent);

        try {
            $requestTransformer->shouldNotHaveReceived('transformLate');
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }
    public function testOnResponseLate()
    {
        list($matcher, $requestTransformer, $responseTransformer) = $this->getMocks();
        $matcher->shouldReceive('matches')->andReturn(true);
        $responseTransformer->shouldReceive('transformLate');

        $subject = $this->getSubject([$matcher, $requestTransformer, $responseTransformer]);

        $mockEvent = m::mock('Symfony\Component\HttpKernel\Event\FilterResponseEvent');
        $mockEvent->shouldReceive('getRequest')->andReturn(m::mock('Symfony\Component\HttpFoundation\Request'));
        $mockEvent->shouldReceive('getRequestType');
        $mockEvent->shouldReceive('getResponse')->andReturn(m::mock('Symfony\Component\HttpFoundation\Response'));
//        $mockEvent->shouldReceive('setResponse');

        $subject->onResponseLate($mockEvent);

        try {
            $responseTransformer->shouldHaveReceived('transformLate')->between(1, 1);
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }

    protected function methodIsBound($method, $testEvent)
    {
        foreach (IOEventSubscriber::getSubscribedEvents() as $event => $listeners) {
            foreach ($listeners as $listener) {
                list ($listener) = $listener;
                if ($listener == $method && $event == $testEvent) {
                    $this->assertTrue(true);
                    return;
                }
            }
        }

        $this->assertTrue(false, $method . ' is not bound to event ' . $testEvent);
    }
}
