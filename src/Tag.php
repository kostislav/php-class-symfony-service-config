<?php


namespace Kostislav\ClassConfig;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Tag {
    private string $name;
    private array $attributes;

    public function __construct(string $name, array $attributes = []) {
        $this->name = $name;
        $this->attributes = $attributes;
    }

    public function getName() {
        return $this->name;
    }

    public function getAttributes() {
        return $this->attributes;
    }
}