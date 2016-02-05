<?php

namespace MediaMonks\RestApiBundle\Response;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

interface ResponseTransformerInterface
{
    public function transformEarly(Request $request, SymfonyResponse $response);

    public function transformLate(Request $request, SymfonyResponse $response);
}
