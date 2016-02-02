<?php

namespace MediaMonks\RestApiBundle\Model;

use MediaMonks\RestApiBundle\Util\StringUtil;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use MediaMonks\RestApiBundle\Response\Response;
use MediaMonks\RestApiBundle\Exception\FormValidationException;
use MediaMonks\RestApiBundle\Response\PaginatedResponseAbstract;

class ResponseContainer
{
    const ERROR_HTTP_PREFIX = 'error.http.';

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
     * @var array
     */
    protected $pagination;

    /**
     * @var string
     */
    protected $location;

    /**
     * @param mixed $content
     * @return $this
     */
    public static function createAutoDetect($content)
    {
        $container = new self();
        $container->autoDetectContent($content);

        return $container;
    }

    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        if (isset($this->exception)) {
            if ($this->exception instanceof HttpException) {
                return $this->exception->getStatusCode();
            } elseif (
                array_key_exists($this->exception->getCode(), Response::$statusTexts)
                && $this->exception->getCode() >= 400
            ) {
                return $this->exception->getCode();
            } else {
                return Response::HTTP_INTERNAL_SERVER_ERROR;
            }
        }

        return $this->statusCode;
    }

    /**
     * @param mixed $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getReturnStatusCode()
    {
        return $this->returnStatusCode;
    }

    /**
     * @param mixed $returnStatusCode
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
     * @return mixed
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @param mixed $exception
     * @return $this
     */
    public function setException($exception)
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
     * @param array $pagination
     */
    public function setPagination($pagination)
    {
        $this->pagination = $pagination;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $location
     * @return $this
     */
    public function setLocation($location)
    {
        $this->location = $location;

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
            if ($this->exception instanceof FormValidationException) {
                $error = $this->exception->toArray();
            } elseif ($this->exception instanceof HttpException) {
                $error = [
                    'code'    => self::ERROR_HTTP_PREFIX .
                        StringUtil::classToSnakeCase($this->exception, 'HttpException'),
                    'message' => $this->exception->getMessage()
                ];
            } else {
                $error = [
                    'code'    => $this->exception->getCode(),
                    'message' => $this->exception->getMessage()
                ];
            }
            //$error['message'] = trim($this->getApp()->trans($error['message']));
            $return['error'] = $error;
        }

        if (isset($this->data)) {
            $return['data'] = $this->data;
        }

        if (isset($this->location)) {
            $return['location'] = $this->location;
        }

        if (isset($this->pagination)) {
            $return['pagination'] = $this->pagination;
        }

        return $return;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return (empty($this->exception) && is_null($this->data));
    }

    /**
     * @param $content
     * @return $this
     */
    public function autoDetectContent($content)
    {
        if ($content instanceof \Exception) {
            $this->setException($content);
        } elseif ($content instanceof PaginatedResponseAbstract) {
            $this->setPagination($content->toArray());
            $this->setData($content->getData());
        } elseif ($content instanceof RedirectResponse) {
            $this->setLocation($content->headers->get('Location'));
            $this->setStatusCode($content->getStatusCode());
        } elseif ($content instanceof SymfonyResponse) {
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
        $data['error']['code'] = 'api.handler.error';

        return json_encode($data);
    }
}
