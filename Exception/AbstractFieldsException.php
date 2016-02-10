<?php

namespace MediaMonks\RestApiBundle\Exception;

abstract class AbstractFieldsException extends AbstractException implements ExceptionInterface, FieldExceptionInterface
{
    /**
     * AbstractFieldsException constructor.
     * @param string $message
     * @param string $code
     */
    public function __construct($message, $code)
    {
        $this->message = $message;
        $this->code = $code;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $return = [
            'code'     => $this->getCode(),
            'messsage' => $this->getMessage()
        ];
        foreach ($this->getFields() as $field) {
            $return['fields'][] = $field->toArray();
        }
        return $return;
    }
}
