<?php

namespace FakeServices;

class CombinedService {
    private SimpleService $service1;
    private SimpleService $service2;

    public function __construct(SimpleService $service1, SimpleService $service2) {
        $this->service1 = $service1;
        $this->service2 = $service2;
    }

    public function combinedValue(): string {
        return $this->service1->value() . ' ' . $this->service2->value();
    }
}