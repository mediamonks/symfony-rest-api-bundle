<?php

namespace MediaMonks\RestApiBundle\Exception;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Response;
use MediaMonks\RestApiBundle\Model\ResponseContainer;

class FormValidationException extends \Exception
{
    const ERROR_MESSAGE = 'error.form.validation';
    const FIELD_ERROR_PREFIX = 'error.form.validation.';

    /**
     * @var Form
     */
    protected $form;

    /**
     * @param Form $form
     * @param string $message
     * @param \Exception|int $code
     */
    public function __construct(Form $form, $message = self::ERROR_MESSAGE, $code = Response::HTTP_BAD_REQUEST)
    {
        $this->form    = $form;
        $this->message = $message;
        $this->code    = $code;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'code'    => self::ERROR_MESSAGE,
            'message' => self::ERROR_MESSAGE,
            'fields'  => $this->getFieldErrors()
        ];
    }

    /**
     * @return array
     */
    public function getFieldErrors()
    {
        return $this->getErrorMessages($this->form);
    }

    /**
     * @param Form $form
     * @return array
     */
    protected function getErrorMessages(Form $form)
    {
        $errors = [];
        foreach ($form->getErrors() as $key => $error) {
            if ($form->isRoot()) {
                $errors[] = $this->toErrorArray($error);
            } else {
                $errors[] = $this->toErrorArray($error, $form);
            }
        }
        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                foreach ($this->getErrorMessages($child) as $error) {
                    $errors[] = $error;
                }
            }
        }

        return $errors;
    }

    /**
     * @param $error
     * @param null $child
     * @return array
     */
    protected function toErrorArray($error, $child = null)
    {
        $data = [];
        if (is_null($child)) {
            $data['field'] = '#';
        } else {
            $data['field'] = $child->getName();
        }
        if (!is_null($error->getCause()) && !is_null($error->getCause()->getConstraint())) {
            $data['code'] = self::FIELD_ERROR_PREFIX . StringUtil::classToSnakeCase(
                    $error->getCause()->getConstraint()
                );
        } else {
            if (stristr($error->getMessage(), 'csrf')) {
                $data['code'] = self::FIELD_ERROR_PREFIX . 'csrf';
            } else {
                $data['code'] = self::FIELD_ERROR_PREFIX . 'general';
            }
        }
        $data['message'] = $error->getMessage();

        return $data;
    }
}