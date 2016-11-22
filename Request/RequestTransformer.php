<?php

namespace MediaMonks\RestApiBundle\Request;

use Symfony\Component\HttpFoundation\Request;

class RequestTransformer implements RequestTransformerInterface
{
    /**
     * @var array
     */
    protected $outputFormats = [];

    /**
     * RequestModifier constructor.
     * @param array $outputFormats
     */
    public function __construct(array $outputFormats)
    {
        $this->outputFormats = $outputFormats;
    }

    /**
     * @param Request $request
     */
    public function transform(Request $request)
    {
        $this->acceptJsonBody($request);
        $this->setRequestFormat($request);
    }

    /**
     * @param Request $request
     */
    protected function acceptJsonBody(Request $request)
    {
        if (strpos($request->headers->get('Content-Type'), 'application/json') === 0) {
            $data = json_decode($request->getContent(), true);
            $request->request->replace(is_array($data) ? $data : []);
        }
    }

    /**
     * @param Request $request
     */
    protected function setRequestFormat(Request $request)
    {
        $default = Format::getDefault();
        $format = $request->getRequestFormat($request->query->get('_format', $default));
        if (!in_array($format, $this->outputFormats)) {
            $format = $default;
        }
        $request->setRequestFormat($format);
    }
}
