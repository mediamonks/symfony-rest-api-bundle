<?php

namespace MediaMonks\RestApiBundle\Model;

use MediaMonks\RestApiBundle\Exception\AbstractValidationException;
use MediaMonks\RestApiBundle\Exception\ExceptionInterface;
use MediaMonks\RestApiBundle\Response\Error;
use MediaMonks\RestApiBundle\Response\PaginatedResponseInterface;
use MediaMonks\RestApiBundle\Util\StringUtil;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ResponseModel
{
    const EXCEPTION_GENERAL = 'Exception';
    const EXCEPTION_HTTP = 'HttpException';

    /**
     * @var int
     */
    protected $statusCode = Response::HTTP_OK;

    /**
     * @var bool
     */
    protected $returnStatusCode = false;

    /**
     * @var bool
     */
    protected $returnStackTrace = false;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var \Exception
     */
    protected $exception;

    /**
     * @var PaginatedResponseInterface
     */
    protected $pagination;

    /**
     * @return boolean
     */
    public function isReturnStackTrace()
    {
        return $this->returnStackTrace;
    }

    /**
     * @param boolean $returnStackTrace
     * @return $this
     */
    public function setReturnStackTrace($returnStackTrace)
    {
        $this->returnStackTrace = $returnStackTrace;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        if (isset($this->response)) {
            return $this->response->getStatusCode();
        }
        if (isset($this->exception)) {
            return $this->getExceptionStatusCode();
        }
        if ($this->isEmpty()) {
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
        }
        if ($this->exception instanceof AbstractValidationException) {
            return Response::HTTP_BAD_REQUEST;
        }
        if ($this->isValidHttpStatusCode($this->exception->getCode())) {
            return $this->exception->getCode();
        }

        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    /**
     * @param int $code
     * @return bool
     */
    protected function isValidHttpStatusCode($code)
    {
        return array_key_exists($code, Response::$statusTexts) && $code >= Response::HTTP_BAD_REQUEST;
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
     * @return PaginatedResponseInterface
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
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param Response $response
     * @return ResponseModel
     */
    public function setResponse(Response $response)
    {
        $this->response = clone $response;
        $this->setStatusCode($response->getStatusCode());
        $this->setData($response->getContent());

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
        } elseif (isset($this->response) && $this->response instanceof RedirectResponse) {
            $return['location'] = $this->response->headers->get('Location');
        } else {
            $return += $this->dataToArray();
        }

        return $return;
    }

    /**
     * @return array
     */
    protected function dataToArray()
    {
        $return = [];
        if (isset($this->data)) {
            $return['data'] = $this->data;
            if (isset($this->pagination)) {
                $return['pagination'] = $this->pagination->toArray();
            }
        }

        return $return;
    }

    /**
     * @return array
     */
    protected function exceptionToArray()
    {
        if ($this->exception instanceof ExceptionInterface) {
            $error = $this->exception->toArray();
        } elseif ($this->exception instanceof HttpException) {
            $error = $this->httpExceptionToArray();
        } else {
            $error = $this->generalExceptionToArray();
        }
        if ($this->isReturnStackTrace()) {
            $error['stack_trace'] = $this->getExceptionStackTrace();
        }

        return $error;
    }

    /**
     * @return string
     */
    protected function getExceptionStackTrace()
    {
        $traces = [];
        foreach ($this->exception->getTrace() as $trace) {
            $trace['args'] = json_decode(json_encode($trace['args']), true);
            $traces[] = $trace;
        }

        return $traces;
    }

    /**
     * @return array
     */
    protected function httpExceptionToArray()
    {
        return [
            'code'    => $this->getExceptionErrorCode(Error::CODE_HTTP, self::EXCEPTION_HTTP),
            'message' => $this->exception->getMessage(),
        ];
    }

    /**
     * @return array
     */
    protected function generalExceptionToArray()
    {
        return [
            'code'    => trim($this->getExceptionErrorCode(Error::CODE_GENERAL, self::EXCEPTION_GENERAL), '.'),
            'message' => $this->exception->getMessage(),
        ];
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
            !isset($this->exception)
            && is_null($this->data)
            && !isset($this->pagination)
            && $this->isEmptyResponse()
        );
    }

    /**
     * @return bool
     */
    protected function isEmptyResponse()
    {
        return !isset($this->response) || $this->response->isEmpty();
    }

    // @codeCoverageIgnoreStart
    /**
     * This is called when an exception is thrown during the response transformation
     *
     * @return string
     */
    public function __toString()
    {
        $data = $this->toArray();
        $data['error']['code'] = Error::CODE_REST_API_BUNDLE;

        return json_encode($data);
    }
    // @codeCoverageIgnoreEnd
}
