<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Tests\Impl;

use SmartAssert\ServiceClient\ObjectFactory\ObjectDefinitionInterface;
use SmartAssert\ServiceClient\ObjectFactory\StringAccessor;

class DoubleNonEmptyStringObjectDefinition implements ObjectDefinitionInterface
{
    /**
     * @param non-empty-string $key1
     * @param non-empty-string $key2
     */
    public function __construct(
        private readonly string $key1,
        private readonly string $key2,
    ) {
    }

    public function getAccessors(): array
    {
        return [
            new StringAccessor($this->key1),
            new StringAccessor($this->key2),
        ];
    }

    public function isValid(array $data): bool
    {
        return (isset($data[0]) && is_string($data[0]) && '' !== $data[0])
            && (isset($data[1]) && is_string($data[1]) && '' !== $data[1]);
    }

    /**
     * @param array{0: non-empty-string, 1: non-empty-string} $data
     */
    public function create(array $data): DoubleNonEmptyStringObject
    {
        return new DoubleNonEmptyStringObject($data[0], $data[1]);
    }
}
