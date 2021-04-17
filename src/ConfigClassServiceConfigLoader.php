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
    private ContainerBuilder $container;

    public function __construct(ContainerBuilder $container) {
        $this->container = $container;
    }

    public function supports($resource, string $type = null) {
        return class_exists($resource);
    }

    public function load($resource, string $type = null) {
        $class = new ReflectionClass($resource);
        $configServiceName = $class->getName();

        if ($this->container->hasDefinition($configServiceName)) {
            return;
        }

        $importAnnotation = $this->getAttributeOrNull($class, Import::class);
        if ($importAnnotation != null) {
            foreach($importAnnotation->getConfigClasses() as $importedConfig) {
                $this->load($importedConfig);
            }
        }

        $this->container->setDefinition($configServiceName, new Definition($configServiceName));
        foreach ($class->getMethods() as $method) {
            if ($method->getModifiers() & ReflectionMethod::IS_STATIC) {
                continue;
            }
            $methodName = $method->getName();
            $serviceDefinitionAnnotation = $this->getAttributeOrNull($method, ServiceDefinition::class);
            if ($serviceDefinitionAnnotation != null) {
                $returnType = $method->getReturnType();
                if ($returnType == null) {
                    throw new InvalidArgumentException(sprintf('Method %s does not specify a return type.', $methodName));
                }
                $args = [];
                foreach ($method->getParameters() as $parameter) {
                    $parameterAnnotation = $this->getAttributeOrNull($parameter, Parameter::class);
                    if ($parameterAnnotation != null) {
                        $parameterName = $parameterAnnotation->getName() ?? $parameter->getName();
                        $args[] = "%{$parameterName}%";
                    } else {
                        $dependencyName = $parameter->getName();
                        $serviceAnnotation = $this->getAttributeOrNull($parameter, Service::class);
                        if ($serviceAnnotation != null) {
                            $dependencyName = $serviceAnnotation->getName();
                        }
                        $args[] = new Reference($dependencyName);
                    }
                }
                $definition = new Definition($returnType, $args);
                $definition->setPublic($serviceDefinitionAnnotation->isPublic());
                $serviceName = $serviceDefinitionAnnotation->getName() ?? $method->getName();

                foreach($method->getAttributes(Tag::class) as $tagAttribute) {
                    $tag = $tagAttribute->newInstance();
                    $definition->addTag($tag->getName(), $tag->getAttributes());
                }

                $definition->setFactory([new Reference($configServiceName), $methodName]);
                $this->container->setDefinition($serviceName, $definition);
            }
        }
    }

    private function getAttributeOrNull($element, $attributeClass) {
        $attributes = $element->getAttributes($attributeClass);
        if (empty($attributes)) {
            return null;
        } else {
            return $attributes[0]->newInstance();
        }
    }
}