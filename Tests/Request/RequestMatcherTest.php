<?php

namespace MediaMonks\RestApiBundle\Tests\Request;

use MediaMonks\RestApiBundle\Request\RequestMatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class RequestMatcherTest extends \PHPUnit_Framework_TestCase
{

    public function testMatchesEmptyWhitelist()
    {
        $matcher = new RequestMatcher([]);
        foreach ([
                     ['path' => '/foo', 'type' => HttpKernelInterface::MASTER_REQUEST, 'result' => false],
                     ['path' => '/bar', 'type' => HttpKernelInterface::MASTER_REQUEST, 'result' => false],
                     ['path' => '/foo', 'type' => HttpKernelInterface::SUB_REQUEST, 'result' => false],
                     ['path' => '/bar', 'type' => HttpKernelInterface::SUB_REQUEST, 'result' => false],
                 ] as $test
        ) {
            $this->assertEquals($test['result'],
                $matcher->matches($this->getRequestFromPath($test['path']), $test['type']));
        }
    }

    public function testMatchesWhitelist()
    {
        $matcher = new RequestMatcher([
            '~^/api$~',
            '~^/api/~'
        ]);
        foreach ([
                     ['path' => '/foo', 'type' => HttpKernelInterface::MASTER_REQUEST, 'result' => false],
                     ['path' => '/foo', 'type' => HttpKernelInterface::MASTER_REQUEST, 'result' => false],
                     ['path' => '/fapi', 'type' => HttpKernelInterface::MASTER_REQUEST, 'result' => false],
                     ['path' => '/api', 'type' => HttpKernelInterface::MASTER_REQUEST, 'result' => true],
                     ['path' => '/api', 'type' => HttpKernelInterface::SUB_REQUEST, 'result' => false],
                     ['path' => '/api/', 'type' => HttpKernelInterface::MASTER_REQUEST, 'result' => true],
                     ['path' => '/api/', 'type' => HttpKernelInterface::SUB_REQUEST, 'result' => false],
                     ['path' => '/api/foo', 'type' => HttpKernelInterface::MASTER_REQUEST, 'result' => true],
                     ['path' => '/api/doc', 'type' => HttpKernelInterface::MASTER_REQUEST, 'result' => true],
                 ] as $test
        ) {
            $this->assertEquals($test['result'],
                $matcher->matches($this->getRequestFromPath($test['path']), $test['type']));
        }
    }

    public function testMatchesWhitelistBlacklist()
    {
        $matcher = new RequestMatcher([
            '~^/api$~',
            '~^/api/~'
        ], [
            '~^/api/doc~'
        ]);
        foreach ([
                     ['path' => '/foo', 'type' => HttpKernelInterface::MASTER_REQUEST, 'result' => false],
                     ['path' => '/foo', 'type' => HttpKernelInterface::MASTER_REQUEST, 'result' => false],
                     ['path' => '/fapi', 'type' => HttpKernelInterface::MASTER_REQUEST, 'result' => false],
                     ['path' => '/api', 'type' => HttpKernelInterface::MASTER_REQUEST, 'result' => true],
                     ['path' => '/api', 'type' => HttpKernelInterface::SUB_REQUEST, 'result' => false],
                     ['path' => '/api/', 'type' => HttpKernelInterface::MASTER_REQUEST, 'result' => true],
                     ['path' => '/api/', 'type' => HttpKernelInterface::SUB_REQUEST, 'result' => false],
                     ['path' => '/api/foo', 'type' => HttpKernelInterface::MASTER_REQUEST, 'result' => true],
                     ['path' => '/api/doc', 'type' => HttpKernelInterface::MASTER_REQUEST, 'result' => false],
                     ['path' => '/api/doc', 'type' => HttpKernelInterface::SUB_REQUEST, 'result' => false],
                 ] as $test
        ) {
            $this->assertEquals($test['result'],
                $matcher->matches($this->getRequestFromPath($test['path']), $test['type']));
        }
    }

    public function testMatchedRequestIsMarkedAsMatched()
    {
        $matcher = new RequestMatcher(['~^/api$~']);
        $request = $this->getRequestFromPath('/api');

        $this->assertEquals(true, $matcher->matches($request));
        $this->assertTrue($request->attributes->has(RequestMatcher::ATTRIBUTE_MATCHED));
        $this->assertEquals(true, $matcher->matches($request));
    }

    public function testNonMatchedRequestIsNotMarkedAsMatched()
    {
        $matcher = new RequestMatcher(['~^/api$~']);
        $request = $this->getRequestFromPath('/');

        $this->assertEquals(false, $matcher->matches($request));
        $this->assertFalse($request->attributes->has(RequestMatcher::ATTRIBUTE_MATCHED));
    }

    public function testMatchedRequestIsNotMatchedTwice()
    {
        $matcher = new RequestMatcher(['~^/api$~']);
        $request = $this->getRequestFromPath('/');

        $this->assertEquals(false, $matcher->matches($request));
        $this->assertFalse($request->attributes->has(RequestMatcher::ATTRIBUTE_MATCHED));
        $this->assertEquals(false, $matcher->matches($request));
    }

    public function testMatchesAlreadyMatched()
    {
        $subject = new RequestMatcher(['~^/api$~']);
        $request = $this->getRequestFromPath('/api');

        // First match, path 1
        $this->assertTrue($subject->matches($request));
        // Second match, shortcut path 2
        $this->assertTrue($subject->matches($request));
    }

    /**
     * @param string $path
     * @return Request
     */
    protected function getRequestFromPath($path)
    {
        return Request::create($path);
    }
}
