<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\ObjectFactory;

use SmartAssert\ServiceClient\ArrayAccessor;

class PositiveIntegerAccessor implements AccessorInterface
{
    /**
     * @param non-empty-string $key
     */
    public function __construct(
        private readonly string $key
    ) {
    }

    /**
     * @return null|positive-int
     */
    public function get(ArrayAccessor $arrayAccessor, array $data): ?int
    {
        return $arrayAccessor->getPositiveInteger($this->key, $data);
    }
}
