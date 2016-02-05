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
        if($requestType !== HttpKernelInterface::MASTER_REQUEST) {
            return false;
        }

        if ($request->attributes->getBoolean(self::ATTRIBUTE_MATCHED) === true) {
            return true;
        }

        return $this->matchAgainstWhitelistAndBlacklist($request);
    }

    /**
     * @param Request $request
     * @return bool
     */
    protected function matchAgainstWhitelistAndBlacklist(Request $request)
    {
        foreach ($this->blacklist as $blacklist) {
            if (preg_match($blacklist, $request->getPathInfo())) {
                return false;
            }
        }

        foreach ($this->whitelist as $whitelist) {
            if (preg_match($whitelist, $request->getPathInfo())) {
                $request->attributes->set(self::ATTRIBUTE_MATCHED, true);
                return true;
            }
        }

        return false;
    }
}
