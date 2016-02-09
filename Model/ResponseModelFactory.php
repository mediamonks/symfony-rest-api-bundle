<?php

namespace MediaMonks\RestApiBundle\Model;

use MediaMonks\RestApiBundle\Response\PaginatedResponseInterface;
use MediaMonks\RestApiBundle\Response\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
