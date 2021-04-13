<?php

use ConfigClasses\Valid\SimpleConfig;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\equalTo;

include __DIR__ . '/../vendor/phpunit/phpunit/src/Framework/Assert/Functions.php';

class SymfonyIntegrationTest extends TestCase {
    /** @test */
    function createsSimpleController() {
        $kernel = new TestKernel([SimpleConfig::class]);
        $service = $kernel->getService('someService');

        assertThat($service->value(), equalTo('sesd'));
    }
}