<?php

namespace AppBundle\Controller;

use MediaMonks\RestApiBundle\Exception\ErrorField;
use MediaMonks\RestApiBundle\Exception\ErrorFieldCollection;
use MediaMonks\RestApiBundle\Exception\ValidationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use MediaMonks\RestApiBundle\Exception\FormValidationException;
use MediaMonks\RestApiBundle\Response\CursorPaginatedResponse;
use MediaMonks\RestApiBundle\Response\OffsetPaginatedResponse;

/**
 * @Route("/api/")
 */
class ApiController extends Controller
{
    /**
     * @Route("empty")
     */
    public function emptyAction()
    {
        return;
    }

    /**
     * @Route("string")
     */
    public function stringAction()
    {
        return 'foobar';
    }

    /**
     * @Route("integer")
     */
    public function integerAction()
    {
        return 42;
    }

    /**
     * @Route("array")
     */
    public function arrayAction()
    {
        return ['foo', 'bar'];
    }

    /**
     * @Route("object")
     */
    public function objectAction()
    {
        $object      = new \stdClass();
        $object->foo = 'bar';
        return $object;
    }

    /**
     * @Route("symfony")
     */
    public function symfonyResponseAction()
    {
        return new Response('foobar', Response::HTTP_CREATED);
    }

    /**
     * @Route("paginated/offset")
     */
    public function offsetPaginatedAction()
    {
        return new OffsetPaginatedResponse('foobar', 30, 10, 56);
    }

    /**
     * @Route("paginated/cursor")
     */
    public function cursorPaginatedAction()
    {
        return new CursorPaginatedResponse('foobar', 1, 10, 10, 56);
    }

    /**
     * @Route("redirect")
     */
    public function symfonyRedirectAction()
    {
        return $this->redirect('http://www.mediamonks.com', Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("exception")
     */
    public function exceptionAction()
    {
        throw new \Exception('Foo'); // will return 500 Internal Server Error
    }

    /**
     * @Route("exception-invalid-http-status-code")
     */
    public function exceptionInvalidHttpStatusCodeAction()
    {
        throw new \Exception('foo', 900); // will return 500 Internal Server Error
    }

    /**
     * @Route("exception-valid-http-status-code")
     */
    public function exceptionValidCodeAction()
    {
        throw new \Exception('foo', Response::HTTP_BAD_REQUEST); // will return 400 Bad Request
    }

    /**
     * @Route("exception-not-found")
     */
    public function symfonyNotFoundExceptionAction()
    {
        throw new NotFoundHttpException('foo'); // will return 404 Not Found
    }

    /**
     * @Route("exception-form")
     */
    public function formValidationExceptionAction()
    {
        $form = $this->createFormBuilder()->getForm();
        $form->submit([]);
        if (!$form->isValid()) {
            throw new FormValidationException($form);
        }
        return new Response('foobar', Response::HTTP_CREATED);
    }

    /**
     * @Route("exception-validation")
     */
    public function validationExceptionAction()
    {
        throw new ValidationException([
            new ErrorField('field', 'code', 'message')
        ]);
    }
}
