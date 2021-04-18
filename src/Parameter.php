<?php

namespace Kostislav\ClassConfig;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class Parameter {
    private ?string $name;

    /**
     * @param $name : The name of the container parameter to look up. If null, the name of the method parameter will be used.
     */
    public function __construct(?string $name = null) {
        $this->name = $name;
    }

    public function getName(): ?string {
        return $this->name;
    }
}