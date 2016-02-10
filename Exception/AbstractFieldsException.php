<?php

namespace MediaMonks\RestApiBundle\Exception;

abstract class AbstractFieldsException extends AbstractException implements ExceptionInterface, FieldExceptionInterface
{
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
