Step 3: Using the bundle
========================

Basic Usage
-----------

Using this bundle is very easy, you can simply return scalars, arrays or objects from your controller and the bundle
will serialize and output the content according to the specification.

.. code-block:: php

    <?php

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;

    class ExampleController extends Controller
    {
        public function integerAction()
        {
            return 42;
        }

        public function stringAction()
        {
            return 'foobar';
        }

        public function arrayAction()
        {
            return ['foo', 'bar'];
        }

        public function objectAction()
        {
            return new \AppBundle\Entity\Example(); // can be used when using JMS Serializer
        }
    }

.. note::

    When returning an object you will have to make sure it's properly configured for use with JMS Serializer because
    otherwise the object will be returned totally empty.

Custom Status Code And Headers
------------------------------

It is also possible to return a regular Symfony HttpFoundation Response which allows you to set a custom http status
code and headers.

.. code-block:: php

    <?php

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Response;

    class ExampleController extends Controller
    {
        public function symfonyResponseAction()
        {
            return new Response('foobar', Response::HTTP_CREATED, ['X-My-Header' => 'My Value']);
        }
    }

.. note::

    If you want to return a non-scalar response instead but still want to have control over your headers you can return
    an instance of MediaMonks\RestApiBundle\Response\Response instead.

Pagination
----------

.. code-block:: php

    <?php

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use MediaMonks\RestApiBundle\Response\CursorPaginatedResponse;
    use MediaMonks\RestApiBundle\Response\OffsetPaginatedResponse;

    class ExampleController extends Controller
    {
        public function offsetPaginatedAction()
        {
            return new OffsetPaginatedResponse(['foo', 'bar'], 30, 10, 56);
        }

        public function cursorPaginatedAction()
        {
            return new CursorPaginatedResponse(['foo', 'bar'], 1, 10, 10, 56);
        }
    }

Exceptions
----------

Exceptions will be automatically converted to a correct error response with best matching http status code.

.. code-block:: php

    <?php

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
    use MediaMonks\RestApiBundle\Exception\ErrorField;
    use MediaMonks\RestApiBundle\Exception\FormValidationException;
    use MediaMonks\RestApiBundle\Exception\ValidationException;

    class ExampleController extends Controller
    {
        public function exceptionAction()
        {
            throw new \Exception('Foo'); // will return 500 Internal Server Error
        }

        public function exceptionInvalidHttpStatusCodeAction()
        {
            throw new \Exception('foo', 900); // will return 500 Internal Server Error
        }

        public function exceptionValidCodeAction()
        {
            throw new \Exception('foo', Response::HTTP_BAD_REQUEST); // will return 400 Bad Request
        }

        public function symfonyNotFoundExceptionAction()
        {
            throw new NotFoundHttpException('foo'); // will return 404 Not Found
        }

        public function formValidationExceptionAction(Request $request)
        {
            $form = $this->createFormBuilder()->getForm();
            $form->handleRequest($request);
            if (!$form->isValid()) {
                throw new FormValidationException($form);
            }
            // other code for handling your form
        }

        public function customValidationExceptionAction(Request $request)
        {
            throw new ValidationException([
                new ErrorField('field', 'code', 'message')
            ]);
        }
    }
