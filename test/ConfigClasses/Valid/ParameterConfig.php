<?php

namespace ConfigClasses\Valid;

use FakeServices\SimpleService;
use Kostislav\ClassConfig\Parameter;
use Kostislav\ClassConfig\ServiceDefinition;

class ParameterConfig {
    #[ServiceDefinition(isPublic: true)]
    public function parameterizedService(#[Parameter] string $param1, #[Parameter('param.two')]string $param2): SimpleService {
        return new SimpleService($param1 . ' ' . $param2);
    }
}