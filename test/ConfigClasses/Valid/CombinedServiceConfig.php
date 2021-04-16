<?php

namespace ConfigClasses\Valid;

use FakeServices\CombinedService;
use FakeServices\SimpleService;
use Kostislav\ClassConfig\ServiceDefinition;

class CombinedServiceConfig {
    #[ServiceDefinition]
    public function innerService1(): SimpleService {
        return new SimpleService('sesd');
    }

    #[ServiceDefinition]
    public function innerService2(): SimpleService {
        return new SimpleService('dada');
    }

    #[ServiceDefinition(isPublic: true)]
    public function outerService(SimpleService $innerService1, SimpleService $innerService2): CombinedService {
        return new CombinedService($innerService1, $innerService2);
    }
}