<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\ObjectFactory;

interface ObjectDefinitionInterface
{
    /**
     * @return AccessorInterface[]
     */
    public function getAccessors(): array;

    /**
     * @param array<mixed> $data
     */
    public function isValid(array $data): bool;

    /**
     * @param array<mixed> $data
     */
    public function create(array $data): object;
}
