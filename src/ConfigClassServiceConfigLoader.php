<?php

namespace Kostislav\ClassConfig;

use \ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;

class ConfigClassServiceConfigLoader extends Loader {
    private const CONFIG_OBJECT_SERVICE_NAME = 'injection_config_object';

    private ContainerBuilder $container;

    public function __construct(ContainerBuilder $container) {
        $this->container = $container;
    }

    public function supports($resource, string $type = null) {
        return class_exists($resource);
    }

    public function load($resource, string $type = null) {
        $class = new ReflectionClass($resource);
        $this->container->setDefinition(self::CONFIG_OBJECT_SERVICE_NAME, new Definition($class->name));
        foreach ($class->getMethods() as $method) {
            if ($method->getModifiers() & ReflectionMethod::IS_STATIC) {
                continue;
            }
            $annotation = $method->getAttributes(ServiceDefinition::class)[0]->newInstance();
            $name = $method->name;
            $returnType = $method->getReturnType();
            if ($returnType == null) {
                throw new InvalidArgumentException(sprintf('Method %s does not specify a return type.', $name));
            }
            $args = [];
            foreach ($method->getParameters() as $parameter) {
                $args[] = new Reference($parameter->name);
            }
            $definition = new Definition($returnType, $args);
            $definition->setPublic($annotation->isPublic());
            $definition->setFactory([new Reference(self::CONFIG_OBJECT_SERVICE_NAME), $name]);
            $this->container->setDefinition($name, $definition);
        }
    }
}