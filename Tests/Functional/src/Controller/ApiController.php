<?php

namespace App\Controller;

use App\Form\Type\TestType;
use MediaMonks\RestApi\Exception\ErrorField;
use MediaMonks\RestApi\Exception\ValidationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use MediaMonks\RestApi\Exception\FormValidationException;
use MediaMonks\RestApi\Response\CursorPaginatedResponse;
use MediaMonks\RestApi\Response\OffsetPaginatedResponse;

class ApiController extends AbstractController
{

    public function emptyAction()
    {
        return null;
    }

    public function stringAction()
    {
        return 'foobar';
    }

    public function integerAction()
    {
        return 42;
    }

    public function arrayAction()
    {
        return ['foo', 'bar'];
    }

    public function objectAction()
    {
        $object = new \stdClass();
        $object->foo = 'bar';

        return $object;
    }

    public function symfonyResponseAction()
    {
        return new Response('foobar', Response::HTTP_CREATED);
    }

    public function offsetPaginatedAction()
    {
        return new OffsetPaginatedResponse('foobar', 1, 2, 3);
    }

    public function cursorPaginatedAction()
    {
        return new CursorPaginatedResponse('foobar', 1, 2, 3, 4);
    }

    public function symfonyRedirectAction()
    {
        return $this->redirect(
            'http://www.mediamonks.com',
            Response::HTTP_SEE_OTHER
        );
    }

    public function exceptionAction()
    {
        throw new \Exception('Foo'); // will return 500 Internal Server Error
    }

    public function exceptionInvalidHttpStatusCodeAction()
    {
        throw new \Exception(
            'foo', 900
        ); // will return 500 Internal Server Error
    }

    public function exceptionValidCodeAction()
    {
        throw new \Exception(
            'foo', Response::HTTP_BAD_REQUEST
        ); // will return 400 Bad Request
    }

    public function symfonyNotFoundExceptionAction()
    {
        throw new NotFoundHttpException('foo'); // will return 404 Not Found
    }

    public function emptyFormValidationExceptionAction()
    {
        $form = $this->createFormBuilder()->getForm();
        $form->submit([]);
        $form->addError(new FormError('Some general error at root level.'));
        throw new FormValidationException($form);
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws FormValidationException
     *
     */
    public function formValidationExceptionAction(Request $request)
    {
        $form = $this->createForm(TestType::class);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            throw new FormValidationException($form);
        }

        return new Response('foobar', Response::HTTP_CREATED);
    }

    public function validationExceptionAction()
    {
        throw new ValidationException(
            [
                new ErrorField('field', 'code', 'message'),
            ]
        );
    }
}
