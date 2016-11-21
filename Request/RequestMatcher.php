<?php

namespace MediaMonks\RestApiBundle\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class RequestMatcher implements RequestMatcherInterface
{
    const ATTRIBUTE_MATCHED = 'mediamonks_rest_api_matched';

    /**
     * @var array
     */
    protected $whitelist = [];

    /**
     * @var array
     */
    protected $blacklist = [];

    /**
     * RequestMatcher constructor.
     * @param $whitelist
     * @param array $blacklist
     */
    public function __construct($whitelist, $blacklist = [])
    {
        $this->whitelist = $whitelist;
        $this->blacklist = $blacklist;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function matches(Request $request)
    {
        if ($this->matchPreviouslyMatchedRequest($request)) {
            return true;
        }

        $match = $this->matchRequestPathAgainstLists($request->getPathInfo());

        if ($match) {
            $this->markRequestAsMatched($request);
        }

        return $match;
    }

    /**
     * @param Request $request
     */
    protected function markRequestAsMatched(Request $request)
    {
        $request->attributes->set(self::ATTRIBUTE_MATCHED, true);
    }

    /**
     * @param Request $request
     * @return bool
     */
    protected function matchPreviouslyMatchedRequest(Request $request)
    {
        return $request->attributes->getBoolean(self::ATTRIBUTE_MATCHED);
    }

    /**
     * @param $requestPath
     * @return bool
     */
    protected function matchRequestPathAgainstLists($requestPath)
    {
        if ($this->matchRequestPathAgainstBlacklist($requestPath) === false) {
            return false;
        }
        if ($this->matchRequestPathAgainstWhitelist($requestPath) === true) {
            return true;
        }
        return false;
    }

    /**
     * @param $requestPath
     * @return bool
     */
    protected function matchRequestPathAgainstBlacklist($requestPath)
    {
        foreach ($this->blacklist as $regex) {
            if (preg_match($regex, $requestPath)) {
                return false;
            }
        }
    }

    /**
     * @param $requestPath
     * @return bool
     */
    protected function matchRequestPathAgainstWhitelist($requestPath)
    {
        foreach ($this->whitelist as $regex) {
            if (preg_match($regex, $requestPath)) {
                return true;
            }
        }
    }
}
