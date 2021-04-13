<?php

namespace Kostislav\ClassConfig;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class ServiceDefinition {
    private bool $isPublic;

    public function __construct(bool $isPublic = false) {
        $this->isPublic = $isPublic;
    }

    public function isPublic() {
        return $this->isPublic;
    }
}