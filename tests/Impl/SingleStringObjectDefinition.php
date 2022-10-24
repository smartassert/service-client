<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Tests\Impl;

use SmartAssert\ServiceClient\ObjectFactory\ObjectDefinitionInterface;
use SmartAssert\ServiceClient\ObjectFactory\StringAccessor;

class SingleStringObjectDefinition implements ObjectDefinitionInterface
{
    /**
     * @param non-empty-string $key
     */
    public function __construct(
        private readonly string $key,
    ) {
    }

    public function getAccessors(): array
    {
        return [
            new StringAccessor($this->key),
        ];
    }

    public function isValid(array $data): bool
    {
        return isset($data[0]) && is_string($data[0]);
    }

    /**
     * @param array{0: string} $data
     */
    public function create(array $data): SingleStringObject
    {
        return new SingleStringObject($data[0]);
    }
}
