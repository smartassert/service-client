<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Tests;

class SerializablePayload implements \JsonSerializable
{
    /**
     * @param array<mixed> $payload
     */
    public function __construct(
        private readonly array $payload,
    ) {
    }

    /**
     * @return array<mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->payload;
    }
}
