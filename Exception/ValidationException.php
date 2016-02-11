<?php

namespace MediaMonks\RestApiBundle\Exception;

use MediaMonks\RestApiBundle\Response\Error;

class ValidationException extends AbstractValidationException
{
    /**
     * @var array
     */
    protected $fields;

    /**
     * ValidationException constructor.
     * @param array $fields
     * @param string $message
     * @param \Exception|string $code
     */
    public function __construct(
        array $fields,
        $message = Error::MESSAGE_FORM_VALIDATION,
        $code = Error::CODE_FORM_VALIDATION
    ) {
        $this->setFields($fields);
        parent::__construct($message, $code);
    }

    /**
     * @param array $fields
     */
    public function setFields(array $fields)
    {
        foreach ($fields as $field) {
            if (!$field instanceof ErrorField) {
                throw new \InvalidArgumentException('$field must be an instance of ErrorField');
            }
            $this->fields[] = $field;
        }
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }
}
