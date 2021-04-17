<?php

namespace Kostislav\ClassConfig;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class Parameter {
    private ?string $name;

    public function __construct(?string $name = null) {
        $this->name = $name;
    }

    public function getName(): ?string {
        return $this->name;
    }
}