<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Payload;

class Payload
{
    public function __construct(
        public readonly string $contentType,
        public readonly string $data,
    ) {
    }
}
