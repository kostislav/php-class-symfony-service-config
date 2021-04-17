<?php

namespace Kostislav\ClassConfig;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Import {
    private array $configClasses;

    public function __construct(string... $configClasses) {
        $this->configClasses = $configClasses;
    }

    public function getConfigClasses() {
        return $this->configClasses;
    }
}