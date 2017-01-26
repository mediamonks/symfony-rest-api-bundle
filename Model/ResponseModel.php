<?php

namespace MediaMonks\RestApiBundle\Model;

use MediaMonks\RestApiBundle\Exception\ExceptionInterface;
use MediaMonks\RestApiBundle\Response\Error;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ResponseModel extends AbstractResponseModel implements ResponseModelInterface
{
    const EXCEPTION_GENERAL = 'Exception';
    const EXCEPTION_HTTP = 'HttpException';

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
}
