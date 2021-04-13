<?php

namespace FakeServices;

class SimpleService {
    private string $configValue;

    public function __construct(string $configValue) {
        $this->configValue = $configValue;
    }

    public function value(): string {
        return $this->configValue;
    }
}