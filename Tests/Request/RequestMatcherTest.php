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
                     ['path' => '/foo', 'result' => false],
                     ['path' => '/bar', 'result' => false],
                 ] as $test
        ) {
            $this->assertEquals($test['result'],
                $matcher->matches($this->getRequestFromPath($test['path'])));
        }
    }

    public function testMatchesWhitelist()
    {
        $matcher = new RequestMatcher([
            '~^/api$~',
            '~^/api/~'
        ]);
        foreach ([
                     ['path' => '/foo', 'result' => false],
                     ['path' => '/foo', 'result' => false],
                     ['path' => '/fapi', 'result' => false],
                     ['path' => '/api', 'result' => true],
                     ['path' => '/api/', 'result' => true],
                     ['path' => '/api/foo', 'result' => true],
                     ['path' => '/api/doc', 'result' => true],
                 ] as $test
        ) {
            $this->assertEquals($test['result'],
                $matcher->matches($this->getRequestFromPath($test['path'])));
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
                     ['path' => '/foo', 'result' => false],
                     ['path' => '/foo', 'result' => false],
                     ['path' => '/fapi', 'result' => false],
                     ['path' => '/api', 'result' => true],
                     ['path' => '/api/', 'result' => true],
                     ['path' => '/api/foo', 'result' => true],
                     ['path' => '/api/doc', 'result' => false],
                 ] as $test
        ) {
            $this->assertEquals($test['result'],
                $matcher->matches($this->getRequestFromPath($test['path'])));
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
