<?php

namespace ConfigClasses\Valid;

use FakeServices\SimpleService;
use Kostislav\ClassConfig\ServiceDefinition;
use Kostislav\ClassConfig\Tag;

class TagConfig {
    #[Tag('tag.name', ['attr1' => 'something'])]
    #[ServiceDefinition(isPublic: true)]
    public function service1(): SimpleService {
        return new SimpleService('serv1');
    }

    #[ServiceDefinition(isPublic: true)]
    public function service2(): SimpleService {
        return new SimpleService('serv2');
    }
}