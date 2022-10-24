<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Tests\Impl;

class DoubleNonEmptyStringObject
{
    /**
     * @param non-empty-string $argument1
     * @param non-empty-string $argument2
     */
    public function __construct(
        public readonly string $argument1,
        public readonly string $argument2,
    ) {
    }
}
