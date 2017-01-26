<?php

namespace MediaMonks\RestApiBundle\Model;

use MediaMonks\RestApiBundle\Response\PaginatedResponseInterface;
use Symfony\Component\HttpFoundation\Response;

class ResponseModelFactory
{
    /**
     * @var ResponseModelInterface
     */
    private $responseModel;

    /**
     * @param ResponseModelInterface $responseModel
     */
    public function __construct(ResponseModelInterface $responseModel)
    {
        $this->responseModel = $responseModel;
    }

    /**
     * @param mixed $content
     * @return ResponseModelInterface
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
     * @return ResponseModelInterface
     */
    public function createFromResponse(Response $response)
    {
        return $this->create()->setResponse($response);
    }

    /**
     * @param PaginatedResponseInterface $response
     * @return ResponseModelInterface
     */
    public function createFromPaginatedResponse(PaginatedResponseInterface $response)
    {
        return $this->create()->setPagination($response);
    }

    /**
     * @param \Exception $exception
     * @return ResponseModelInterface
     */
    public function createFromException(\Exception $exception)
    {
        return $this->create()->setException($exception);
    }

    /**
     * @return ResponseModelInterface
     */
    private function create()
    {
        return clone $this->responseModel;
    }
}
