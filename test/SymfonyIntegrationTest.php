<?php

use ConfigClasses\Valid\AlternativeNameConfig;
use ConfigClasses\Valid\CombinedServiceConfig;
use ConfigClasses\Valid\ParameterConfig;
use ConfigClasses\Valid\SimpleConfig;
use ConfigClasses\Valid\TagConfig;
use Kostislav\ClassConfig\ConfigClassServiceConfigLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\TaggedContainerInterface;
use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\equalTo;

include __DIR__ . '/../vendor/phpunit/phpunit/src/Framework/Assert/Functions.php';

class SymfonyIntegrationTest extends TestCase {
    /** @test */
    function createsSimpleController() {
        $container = $this->buildContainer([SimpleConfig::class]);

        $service = $container->get('someService');

        assertThat($service->value(), equalTo('sesd'));
    }

    /** @test */
    function injectsOtherDefinedServices() {
        $container = $this->buildContainer([CombinedServiceConfig::class]);

        $service = $container->get('outerService');

        assertThat($service->combinedValue(), equalTo('sesd dada'));
    }

    /** @test */
    function servicesAreNotPublicByDefault() {
        $container = $this->buildContainer([CombinedServiceConfig::class]);

        self::assertThrows(ServiceNotFoundException::class, fn() => $container->get('innerService1'));
    }

    /** @test */
    function servicesCanBeExplicitlyNamed() {
        $container = $this->buildContainer([AlternativeNameConfig::class]);

        $service = $container->get('publicService');

        assertThat($service->combinedValue(), equalTo('serv1 serv2'));
    }

    /** @test */
    function canInjectParameters() {
        $container = $this->buildContainer([ParameterConfig::class], [
            'param1' => 'aaa',
            'param.two' => 'bbb'
        ]);

        $service = $container->get('parameterizedService');

        assertThat($service->value(), equalTo('aaa bbb'));
    }

    /** @test */
    function canBeTagged() {
        $container = $this->buildContainer([TagConfig::class]);

        $serviceTags = $container->findTaggedServiceIds('tag.name');

        assertThat($serviceTags, equalTo(['service1' => [['attr1' => 'something']]]));
    }

    private function buildContainer(array $configClasses, array $parameters = []): TaggedContainerInterface {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->getParameterBag()->add($parameters);
        $loader = new ConfigClassServiceConfigLoader($containerBuilder);
        foreach ($configClasses as $configClass) {
            $loader->load($configClass);
        }
        $containerBuilder->compile();
        return $containerBuilder;
    }

    private static function assertThrows(string $exceptionClass, callable $body) {
        try {
            $body();
        } catch (Exception $e) {
            assertThat(get_class($e), equalTo($exceptionClass));
        }
    }
}