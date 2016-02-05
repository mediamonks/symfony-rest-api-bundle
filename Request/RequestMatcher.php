<?php

namespace MediaMonks\RestApiBundle\Request;

use Symfony\Component\HttpFoundation\Request;

class RequestMatcher
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
        if ($request->attributes->getBoolean(self::ATTRIBUTE_MATCHED) === true) {
            return true;
        }

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
    }
}
