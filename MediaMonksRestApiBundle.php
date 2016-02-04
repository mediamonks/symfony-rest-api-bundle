<?php

namespace MediaMonks\RestApiBundle;

use MediaMonks\RestApiBundle\DependencyInjection\MediaMonksRestApiExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MediaMonksRestApiBundle extends Bundle
{
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new MediaMonksRestApiExtension();
        }

        return $this->extension;
    }
}
