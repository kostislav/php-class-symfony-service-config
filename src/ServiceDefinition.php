<?php

namespace Kostislav\ClassConfig;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class ServiceDefinition {
    private ?string $name;
    private bool $isPublic;

    /**
     * @param $name : The name of the service this method defines. If null, the name of the method will be used.
     * @param $isPublic : Whether the service should be marked as public in the container.
     */
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