<?php

namespace Kostislav\ClassConfig;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class ServiceDefinition {
    private ?string $name;
    private bool $isPublic;

    public function __construct(?string $name = null, bool $isPublic = false) {
        $this->isPublic = $isPublic;
        $this->name = $name;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function isPublic(): bool {
        return $this->isPublic;
    }
}