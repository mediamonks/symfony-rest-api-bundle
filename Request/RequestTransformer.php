<?php

namespace MediaMonks\RestApiBundle\Request;

use MediaMonks\RestApiBundle\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;

class RequestTransformer implements RequestTransformerInterface
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
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
        if (!in_array($format, $this->serializer->getSupportedFormats())) {
            $format = $this->serializer->getDefaultFormat();
        }
        $request->setRequestFormat($format);
    }
}
