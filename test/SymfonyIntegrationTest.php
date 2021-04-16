<?php

use ConfigClasses\Valid\CombinedServiceConfig;
use ConfigClasses\Valid\SimpleConfig;
use PHPUnit\Framework\TestCase;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\equalTo;

include __DIR__ . '/../vendor/phpunit/phpunit/src/Framework/Assert/Functions.php';

class SymfonyIntegrationTest extends TestCase {
    private $tempDir;

    protected function setUp(): void {
        $this->tempDir = (new TemporaryDirectory())->create();
    }

    protected function tearDown(): void {
        $this->tempDir->delete();
    }

    /** @test */
    function createsSimpleController() {
        $kernel = new TestKernel($this->tempDir->path(), [SimpleConfig::class]);
        $service = $kernel->getService('someService');

        assertThat($service->value(), equalTo('sesd'));
    }

    /** @test */
    function injectsOtherDefinedServices() {
        $kernel = new TestKernel($this->tempDir->path(), [CombinedServiceConfig::class]);
        $service = $kernel->getService('outerService');

        assertThat($service->combinedValue(), equalTo('sesd dada'));
    }
}