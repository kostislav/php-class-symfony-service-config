<?php

use ConfigClasses\Valid\AlternativeNameConfig;
use ConfigClasses\Valid\CombinedServiceConfig;
use ConfigClasses\Valid\SimpleConfig;
use Kostislav\ClassConfig\ConfigClassServiceConfigLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
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

    private function buildContainer(array $configClasses): Container {
        $containerBuilder = new ContainerBuilder();
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