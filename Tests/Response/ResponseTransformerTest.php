<?php

namespace MediaMonks\RestApiBundle\Tests\Response;


use MediaMonks\RestApiBundle\Response\ResponseTransformer;
use \Mockery as m;

class ResponseTransformerTest
{
    protected function getSubject()
    {
        $serializer = m::mock('JMS\Serializer\Serializer');
        $twig = m::mock('Twig_Environment');
        return new ResponseTransformer($serializer, $twig);
    }

    public function testSetOptions()
    {
        $subject = $this->getSubject();
        $options = ['option1', 'option2'];

        $subject->setOptions($options);
    }
}
