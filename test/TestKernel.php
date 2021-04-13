<?php

use Kostislav\ClassConfig\ConfigClassServiceConfigLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel {
    private array $configClasses;

    public function __construct(array $configClasses) {
        parent::__construct('test', true);
        $this->configClasses = $configClasses;
    }

    public function registerBundles() {
        return [];
    }

    public function registerContainerConfiguration(LoaderInterface $loader) {
        $loader->load(function(ContainerBuilder $container) use ($loader) {
            $loader->getResolver()->addLoader(new ConfigClassServiceConfigLoader($container));
            foreach ($this->configClasses as $configClass) {
                $loader->load($configClass);
            }
        });
    }

    public function getService(string $name) {
        $this->boot();
        return $this->getContainer()->get($name);
    }
}