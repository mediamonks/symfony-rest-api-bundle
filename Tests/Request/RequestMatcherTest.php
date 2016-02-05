<?php

namespace MediaMonks\RestApiBundle\Tests\Request;

use MediaMonks\RestApiBundle\Request\RequestMatcher;
use Symfony\Component\HttpFoundation\Request;

class RequestMatcherTest extends \PHPUnit_Framework_TestCase
{

    public function testMatchesEmptyWhitelist()
    {
        $matcher = new RequestMatcher([]);
        foreach ([
                     '/foo' => false,
                     '/bar' => false
                 ] as $path => $result
        ) {
            $this->assertEquals($result, $matcher->matches($this->getRequestFromPath($path)));
        }
    }

    public function testMatchesWhitelist()
    {
        $matcher = new RequestMatcher([
            '~^/api~'
        ]);
        foreach ([
                     '/foo'     => false,
                     '/bar'     => false,
                     '/fapi'    => false,
                     '/api'     => true,
                     '/api/'    => true,
                     '/api/foo' => true,
                     '/api/doc' => true
                 ] as $path => $result
        ) {
            $this->assertEquals($result, $matcher->matches($this->getRequestFromPath($path)));
        }
    }

    public function testMatchesWhitelistBlacklist()
    {
        $matcher = new RequestMatcher([
            '~^/api~'
        ], [
            '~^/api/doc~'
        ]);
        foreach ([
                     '/foo'             => false,
                     '/bar'             => false,
                     '/fapi'            => false,
                     '/api'             => true,
                     '/api/'            => true,
                     '/api/foo'         => true,
                     '/api/doc'         => false,
                     '/api/doc/foo'     => false,
                     '/api/doc/foo/api' => false
                 ] as $path => $result
        ) {
            $this->assertEquals($result, $matcher->matches($this->getRequestFromPath($path)));
        }
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
