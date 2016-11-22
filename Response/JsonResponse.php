<?php

namespace MediaMonks\RestApiBundle\Response;

use Symfony\Component\HttpFoundation\JsonResponse as BaseJsonResponse;

class JsonResponse extends BaseJsonResponse
{
    /**
     * Constructor.
     *
     * @param mixed $data The response data
     * @param int $status The response status code
     * @param array $headers An array of response headers
     */
    public function __construct($data = null, $status = 200, $headers = [])
    {
        parent::__construct('', $status, $headers);

        if (null === $data) {
            $data = new \ArrayObject();
        }

        $this->setContent($data);
    }

    /**
     * We need this because setData() does json encoding already and
     * this messes up the jsonp callback.
     * It is a performance hit to let is decode/encode a second time
     *
     * @param mixed $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->data = $this->content = $content;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCallback()
    {
        return $this->callback;
    }
}
