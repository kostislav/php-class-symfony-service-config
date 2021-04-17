<?php

namespace ConfigClasses\Valid\Import;

use FakeServices\CombinedService;
use FakeServices\SimpleService;
use Kostislav\ClassConfig\Import;
use Kostislav\ClassConfig\ServiceDefinition;

#[Import(ImportingConfig::class, ImportedConfig::class)]
class TransitivelyImportingConfig {
    #[ServiceDefinition(isPublic: true)]
    public function combinedImportingService(SimpleService $importedService, CombinedService $importingService): CombinedService {
        return new CombinedService($importedService, new SimpleService($importingService->combinedValue()));
    }
}