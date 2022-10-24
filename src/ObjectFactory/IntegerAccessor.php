<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\ObjectFactory;

use SmartAssert\ServiceClient\ArrayAccessor;

class IntegerAccessor implements AccessorInterface
{
    /**
     * @param non-empty-string $key
     */
    public function __construct(
        private readonly string $key
    ) {
    }

    public function get(ArrayAccessor $arrayAccessor, array $data): ?int
    {
        return $arrayAccessor->getInteger($this->key, $data);
    }
}
