<?php

namespace MediaMonks\RestApiBundle\Exception;

class ErrorField
{
    /**
     * @var string
     */
    protected $field;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $message;

    /**
     * ErrorField constructor.
     * @param string $field
     * @param string $code
     * @param string $message
     */
    public function __construct($field, $code, $message)
    {
        $this->field   = $field;
        $this->code    = $code;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'field'   => $this->getField(),
            'code'    => $this->getCode(),
            'message' => $this->getMessage()
        ];
    }
}
