<?php

namespace ConfigClasses\Valid\Import;

use FakeServices\CombinedService;
use FakeServices\SimpleService;
use Kostislav\ClassConfig\Import;
use Kostislav\ClassConfig\ServiceDefinition;

#[Import(ImportedConfig::class)]
class ImportingConfig {
    #[ServiceDefinition(isPublic: true)]
    public function importingService(SimpleService $importedService): CombinedService {
        return new CombinedService($importedService, $importedService);
    }
}