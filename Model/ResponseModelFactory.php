<?php

namespace MediaMonks\RestApiBundle\Model;

use MediaMonks\RestApiBundle\Response\PaginatedResponseInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class ResponseModelFactory
{
    /**
     * @param mixed $content
     * @return ResponseModel
     */
    public static function createFromContent($content)
    {
        $responseModel = new ResponseModel();
        if ($content instanceof \Exception) {
            $responseModel->setException($content);
        } elseif ($content instanceof PaginatedResponseInterface) {
            $responseModel->setPagination($content);
        } elseif ($content instanceof RedirectResponse) {
            $responseModel->setRedirect($content);
        } elseif ($content instanceof Response) {
            $responseModel->setData($content->getContent());
            $responseModel->setStatusCode($content->getStatusCode());
        } else {
            $responseModel->setData($content);
        }
        return $responseModel;
    }
}
