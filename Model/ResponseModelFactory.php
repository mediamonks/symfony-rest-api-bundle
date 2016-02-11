<?php

namespace MediaMonks\RestApiBundle\Model;

use MediaMonks\RestApiBundle\Response\PaginatedResponseInterface;
use Symfony\Component\HttpFoundation\Response;

class ResponseModelFactory
{
    /**
     * @param mixed $content
     * @return ResponseModel
     */
    public static function createFromContent($content)
    {
        if ($content instanceof Response) {
            return self::createFromResponse($content);
        }
        if ($content instanceof PaginatedResponseInterface) {
            return self::createFromPaginatedResponse($content);
        }
        if ($content instanceof \Exception) {
            return self::createFromException($content);
        }
        return self::create()->setData($content);
    }

    /**
     * @param Response $response
     * @return ResponseModel
     */
    public static function createFromResponse(Response $response)
    {
        return self::create()->setResponse($response);
    }

    /**
     * @param PaginatedResponseInterface $response
     * @return ResponseModel
     */
    public static function createFromPaginatedResponse(PaginatedResponseInterface $response)
    {
        return self::create()->setPagination($response);
    }

    /**
     * @param \Exception $exception
     * @return ResponseModel
     */
    public static function createFromException(\Exception $exception)
    {
        return self::create()->setException($exception);
    }

    /**
     * @return ResponseModel
     */
    public static function create()
    {
        return new ResponseModel;
    }
}
