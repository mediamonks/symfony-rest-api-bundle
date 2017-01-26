<?php

namespace MediaMonks\RestApiBundle\Response;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface ResponseTransformerInterface
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function transformEarly(Request $request, Response $response);

    /**
     * @param Request $request
     * @param Response $response
     */
    public function transformLate(Request $request, Response $response);

    /**
     * @param $data
     * @return Response
     */
    public function createResponseFromContent($data);
}
