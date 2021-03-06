<?php

namespace Kostislav\ClassConfig;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class Service {
    private string $name;

    public function __construct(string $name) {
        $this->name = $name;
    }

    public function getName(): string {
        return $this->name;
    }
}