<?php

namespace ConfigClasses\Invalid;

use FakeServices\SimpleService;
use Kostislav\ClassConfig\ServiceDefinition;

class NoReturnTypeConfig {
    #[ServiceDefinition(isPublic: true)]
    public function someService() {
        return new SimpleService('sesd');
    }
}