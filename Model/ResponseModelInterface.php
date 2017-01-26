<?php

namespace MediaMonks\RestApiBundle\Model;

use MediaMonks\RestApiBundle\Response\PaginatedResponseInterface;
use Symfony\Component\HttpFoundation\Response;

interface ResponseModelInterface
{
    /**
     * @param Response $response
     * @return ResponseModelInterface
     */
    public function setResponse(Response $response);

    /**
     * @param PaginatedResponseInterface $paginatedResponse
     * @return ResponseModelInterface
     */
    public function setPagination(PaginatedResponseInterface $paginatedResponse);

    /**
     * @param \Exception $exception
     * @return ResponseModelInterface
     */
    public function setException(\Exception $exception);

    /**
     * @param mixed $data
     * @return ResponseModelInterface
     */
    public function setData($data);

    /**
     * @param bool $returnStatusCode
     * @return ResponseModelInterface
     */
    public function setReturnStatusCode($returnStatusCode);

    /**
     * @param bool $returnStackTrace
     * @return ResponseModelInterface
     */
    public function setReturnStackTrace($returnStackTrace);

    /**
     * @return int
     */
    public function getStatusCode();

    /**
     * @return array
     */
    public function toArray();
}
