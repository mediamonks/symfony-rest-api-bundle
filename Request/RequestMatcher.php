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
     * @param int $requestType
     * @return bool
     */
    public function matches(Request $request, $requestType = HttpKernelInterface::MASTER_REQUEST)
    {
        if ($requestType !== HttpKernelInterface::MASTER_REQUEST) {
            return false;
        }

        if ($request->attributes->getBoolean(self::ATTRIBUTE_MATCHED) === true) {
            return true;
        }

        $match = $this->matchRequestPathAgainstLists($request->getPathInfo());

        if ($match) {
            $request->attributes->set(self::ATTRIBUTE_MATCHED, true);
        }

        return $match;
    }

    /**
     * @param $requestPath
     * @return bool
     */
    protected function matchRequestPathAgainstLists($requestPath)
    {
        foreach ($this->blacklist as $regex) {
            if (preg_match($regex, $requestPath)) {
                return false;
            }
        }
        foreach ($this->whitelist as $regex) {
            if (preg_match($regex, $requestPath)) {
                return true;
            }
        }
        return false;
    }
}
