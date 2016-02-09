<?php

namespace MediaMonks\RestApiBundle\Model;

use MediaMonks\RestApiBundle\Exception\FormValidationException;
use MediaMonks\RestApiBundle\Response\AbstractPaginatedResponse;
use MediaMonks\RestApiBundle\Response\Error;
use MediaMonks\RestApiBundle\Response\PaginatedResponseInterface;
use MediaMonks\RestApiBundle\Util\StringUtil;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ResponseModel
{
    /**
     * @var int
     */
    protected $statusCode = Response::HTTP_OK;

    /**
     * @var bool
     */
    protected $returnStatusCode = false;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var \Exception
     */
    protected $exception;

    /**
     * @var PaginatedResponseInterface
     */
    protected $pagination;

    /**
     * @var RedirectResponse
     */
    protected $redirect;

    /**
     * @param mixed $content
     * @return $this
     */
    public static function createAutoDetect($content)
    {
        $responseModel = new self();
        $responseModel->autoDetectContent($content);

        return $responseModel;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        if (isset($this->exception)) {
            return $this->getExceptionStatusCode();
        } elseif (isset($this->redirect)) {
            return $this->redirect->getStatusCode();
        } elseif ($this->isEmpty()) {
            return Response::HTTP_NO_CONTENT;
        }

        return $this->statusCode;
    }

    /**
     * @return int
     */
    protected function getExceptionStatusCode()
    {
        if ($this->exception instanceof HttpException) {
            return $this->exception->getStatusCode();
        } elseif (
            array_key_exists($this->exception->getCode(), Response::$statusTexts)
            && $this->exception->getCode() >= Response::HTTP_BAD_REQUEST
        ) {
            return $this->exception->getCode();
        }

        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    /**
     * @param int $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * @return bool
     */
    public function getReturnStatusCode()
    {
        return $this->returnStatusCode;
    }

    /**
     * @param bool $returnStatusCode
     * @return $this
     */
    public function setReturnStatusCode($returnStatusCode)
    {
        $this->returnStatusCode = $returnStatusCode;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @param \Exception $exception
     * @return $this
     */
    public function setException(\Exception $exception)
    {
        $this->exception = $exception;

        return $this;
    }

    /**
     * @return array
     */
    public function getPagination()
    {
        return $this->pagination;
    }

    /**
     * @param PaginatedResponseInterface $pagination
     * @return $this
     */
    public function setPagination(PaginatedResponseInterface $pagination)
    {
        $this->pagination = $pagination;
        $this->setData($pagination->getData());

        return $this;
    }

    /**
     * @return RedirectResponse
     */
    public function getRedirect()
    {
        return $this->redirect;
    }

    /**
     * @param RedirectResponse $redirect
     * @return $this
     */
    public function setRedirect(RedirectResponse $redirect)
    {
        $this->redirect = $redirect;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $return = [];
        if ($this->getReturnStatusCode()) {
            $return['statusCode'] = $this->getStatusCode();
        }
        if (isset($this->exception)) {
            $return['error'] = $this->exceptionToArray();
        }
        if (isset($this->data)) {
            $return['data'] = $this->data;
        }
        if (isset($this->redirect)) {
            $return['location'] = $this->redirect->headers->get('Location');
        }
        if (isset($this->pagination)) {
            $return['pagination'] = $this->pagination->toArray();
        }
        return $return;
    }

    /**
     * @return array
     */
    protected function exceptionToArray()
    {
        $error = [
            'code'    => trim($this->getExceptionErrorCode(Error::CODE_GENERAL, 'Exception'), '.'),
            'message' => $this->exception->getMessage()
        ];

        if ($this->exception instanceof FormValidationException) {
            $error['code']   = $this->exception->getCode();
            $error['fields'] = $this->exception->getFieldErrors();
        } elseif ($this->exception instanceof HttpException) {
            $error['code'] = $this->getExceptionErrorCode(Error::CODE_HTTP, 'HttpException');
        }
        return $error;
    }

    /**
     * @param string $errorCode
     * @param string $trim
     * @return string
     */
    protected function getExceptionErrorCode($errorCode, $trim = null)
    {
        return sprintf($errorCode, StringUtil::classToSnakeCase($this->exception, $trim));
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return (
            empty($this->exception)
            && is_null($this->data)
            && is_null($this->pagination)
            && is_null($this->redirect)
        );
    }

    /**
     * @param $content
     * @return $this
     */
    public function autoDetectContent($content)
    {
        if ($content instanceof \Exception) {
            $this->setException($content);
        } elseif ($content instanceof PaginatedResponseInterface) {
            $this->setPagination($content);
        } elseif ($content instanceof RedirectResponse) {
            $this->setRedirect($content);
        } elseif ($content instanceof Response) {
            $this->setData($content->getContent());
            $this->setStatusCode($content->getStatusCode());
        } else {
            $this->setData($content);
        }

        return $this;
    }

    /**
     * This is called when an exception is thrown for the second time
     *
     * @return string
     */
    public function __toString()
    {
        $data                  = $this->toArray();
        $data['error']['code'] = Error::CODE_REST_API_BUNDLE;

        return json_encode($data);
    }
}
