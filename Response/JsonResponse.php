<?php

namespace MediaMonks\RestApiBundle\Response;

use Symfony\Component\HttpFoundation\JsonResponse as BaseJsonResponse;

class JsonResponse extends BaseJsonResponse
{
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
}
