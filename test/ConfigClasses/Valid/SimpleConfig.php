<?php

namespace ConfigClasses\Valid;

use FakeServices\SimpleService;
use Kostislav\ClassConfig\ServiceDefinition;

class SimpleConfig {
    #[ServiceDefinition(isPublic: true)]
    public function someService(): SimpleService {
        return new SimpleService('sesd');
    }
}