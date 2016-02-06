<?php

namespace MediaMonks\RestApiBundle;

use MediaMonks\RestApiBundle\DependencyInjection\MediaMonksRestApiExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MediaMonksRestApiBundle extends Bundle
{
    /**
     * @inheritdoc
     */
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new MediaMonksRestApiExtension();
        }

        if ($this->extension !== false) {
            return $this->extension;
        }
    }
}
