<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Tests\Impl;

class SingleStringObject
{
    public function __construct(
        public readonly string $argument,
    ) {
    }
}
