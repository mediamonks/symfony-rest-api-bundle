<?php

namespace MediaMonks\RestApiBundle\Response;

use Symfony\Component\HttpFoundation\Response as BaseResponse;

class Response extends BaseResponse
{
    /**
     * Sets the response content.
     *
     * We need to allow all sorts of content, not just the ones the regular Response setContent() allows
     *
     * @param mixed $content
     * @return Response
     * @api
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }
}
