<?php

namespace MediaMonks\RestApiBundle\Exception;

use MediaMonks\RestApiBundle\Util\StringUtil;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

class FormValidationException extends \Exception
{
    const ERROR_MESSAGE = 'error.form.validation';

    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * @param FormInterface $form
     * @param string $message
     * @param \Exception|int $code
     */
    public function __construct(FormInterface $form, $message = self::ERROR_MESSAGE, $code = Response::HTTP_BAD_REQUEST)
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
     * @param FormInterface $form
     * @return array
     */
    protected function getErrorMessages(FormInterface $form)
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
     * @param FormError $error
     * @param null|FormInterface $child
     * @return array
     */
    protected function toErrorArray(FormError $error, FormInterface $child = null)
    {
        $data = [];
        if (is_null($child)) {
            $data['field'] = '#';
        } else {
            $data['field'] = $child->getName();
        }
        if (!is_null($error->getCause()) && !is_null($error->getCause()->getConstraint())) {
            $data['code'] = $this->getErrorCode(StringUtil::classToSnakeCase($error->getCause()->getConstraint()));
        } else {
            if (stristr($error->getMessage(), 'csrf')) {
                $data['code'] = $this->getErrorCode('csrf');
            } else {
                $data['code'] = $this->getErrorCode('general');
            }
        }
        $data['message'] = $error->getMessage();

        return $data;
    }

    /**
     * @param string $value
     * @return string
     */
    protected function getErrorCode($value)
    {
        return sprintf(self::ERROR_MESSAGE . '.%s', $value);
    }
}
