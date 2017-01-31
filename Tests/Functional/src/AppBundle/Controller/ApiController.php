<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\TestType;
use MediaMonks\RestApi\Exception\ErrorField;
use MediaMonks\RestApi\Exception\ValidationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use MediaMonks\RestApi\Exception\FormValidationException;
use MediaMonks\RestApi\Response\CursorPaginatedResponse;
use MediaMonks\RestApi\Response\OffsetPaginatedResponse;
use Symfony\Component\HttpKernel\Kernel;

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
        return new OffsetPaginatedResponse('foobar', 1, 2, 3);
    }

    /**
     * @Route("paginated/cursor")
     */
    public function cursorPaginatedAction()
    {
        return new CursorPaginatedResponse('foobar', 1, 2, 3, 4);
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
     * @Route("empty-form")
     * @Method(methods={"POST"})
     */
    public function emptyFormValidationExceptionAction()
    {
        $form = $this->createFormBuilder()->getForm();
        $form->submit([]);
        if (!$form->isValid()) {
            $form->addError(new FormError('Some general error at root level.'));
            throw new FormValidationException($form);
        }
        return new Response('foobar', Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws FormValidationException
     *
     * @Route("form")
     * @Method(methods={"POST"})
     */
    public function formValidationExceptionAction(Request $request)
    {
        if (version_compare(Kernel::VERSION, '2.8.0', '>=')) {
            $form = 'AppBundle\Form\Type\TestType';
        }
        else {
            $form = new TestType();
        }

        $form = $this->createForm($form);
        $form->submit($request->request->all());

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
