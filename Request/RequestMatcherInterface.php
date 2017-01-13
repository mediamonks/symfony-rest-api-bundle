<?php

namespace MediaMonks\RestApiBundle\Request;

use Symfony\Component\HttpFoundation\Request;

interface RequestMatcherInterface
{
    public function matches(Request $request);
}
