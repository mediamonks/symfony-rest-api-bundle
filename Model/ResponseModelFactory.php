<?php

namespace MediaMonks\RestApiBundle\Model;

use MediaMonks\RestApiBundle\Response\PaginatedResponseInterface;
use Symfony\Component\HttpFoundation\Response;

class ResponseModelFactory
{
    /**
     * @return ResponseModelFactory
     */
    public static function createFactory()
    {
        return new self();
    }

    /**
     * @param mixed $content
     * @return ResponseModel
     */
    public function createFromContent($content)
    {
        if ($content instanceof Response) {
            return $this->createFromResponse($content);
        }
        if ($content instanceof PaginatedResponseInterface) {
            return $this->createFromPaginatedResponse($content);
        }
        if ($content instanceof \Exception) {
            return $this->createFromException($content);
        }

        return $this->create()->setData($content);
    }

    /**
     * @param Response $response
     * @return ResponseModel
     */
    public function createFromResponse(Response $response)
    {
        return $this->create()->setResponse($response);
    }

    /**
     * @param PaginatedResponseInterface $response
     * @return ResponseModel
     */
    public function createFromPaginatedResponse(PaginatedResponseInterface $response)
    {
        return $this->create()->setPagination($response);
    }

    /**
     * @param \Exception $exception
     * @return ResponseModel
     */
    public function createFromException(\Exception $exception)
    {
        return $this->create()->setException($exception);
    }

    /**
     * @return ResponseModel
     */
    public function create()
    {
        return new ResponseModel;
    }
}
