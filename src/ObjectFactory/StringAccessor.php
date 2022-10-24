<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\ObjectFactory;

use SmartAssert\ServiceClient\ArrayAccessor;

class StringAccessor implements AccessorInterface
{
    /**
     * @param non-empty-string $key
     */
    public function __construct(
        private readonly string $key
    ) {
    }

    public function get(ArrayAccessor $arrayAccessor, array $data): ?string
    {
        return $arrayAccessor->getString($this->key, $data);
    }
}
