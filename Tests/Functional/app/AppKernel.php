<?php

namespace MediaMonks\RestApiBundle\Tests\Functional\app;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

$loader = require __DIR__ . '/../../../vendor/autoload.php';

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        return [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle(),
            new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new \MediaMonks\RestApiBundle\MediaMonksRestApiBundle(),
            new \AppBundle\AppBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir() . '/config/config.yml');
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        return sprintf('%s/../var/cache/%s', $this->rootDir, $this->environment);
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir()
    {
        return sprintf('%s/../var/logs', $this->rootDir);
    }
}