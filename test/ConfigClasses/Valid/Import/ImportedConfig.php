<?php

namespace ConfigClasses\Valid\Import;

use FakeServices\SimpleService;
use Kostislav\ClassConfig\ServiceDefinition;

class ImportedConfig {
    #[ServiceDefinition]
    public function importedService(): SimpleService {
        return new SimpleService('imp');
    }
}