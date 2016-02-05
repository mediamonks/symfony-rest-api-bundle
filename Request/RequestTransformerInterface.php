<?php

namespace MediaMonks\RestApiBundle\Request;

use Symfony\Component\HttpFoundation\Request;

interface RequestTransformerInterface
{
    public function transform(Request $request);
}