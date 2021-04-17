<?php

namespace ConfigClasses\Valid;

use FakeServices\CombinedService;
use FakeServices\SimpleService;
use Kostislav\ClassConfig\Service;
use Kostislav\ClassConfig\ServiceDefinition;

class AlternativeNameConfig {
    #[ServiceDefinition('renamed')]
    public function irrelevantName(): SimpleService {
        return new SimpleService('serv1');
    }

    #[ServiceDefinition('dotted.name')]
    public function irrelevantName2(): SimpleService {
        return new SimpleService('serv2');
    }

    #[ServiceDefinition(isPublic: true)]
    public function publicService(
        SimpleService $renamed,
        #[Service('dotted.name')] SimpleService $another
    ): CombinedService {
        return new CombinedService($renamed, $another);
    }
}