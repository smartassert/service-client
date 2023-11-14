<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient;

interface SerializableInterface
{
    /**
     * @return array<mixed>
     */
    public function toArray(): array;
}
