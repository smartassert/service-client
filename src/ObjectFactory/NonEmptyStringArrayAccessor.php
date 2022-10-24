<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\ObjectFactory;

use SmartAssert\ServiceClient\ArrayAccessor;

class NonEmptyStringArrayAccessor implements AccessorInterface
{
    /**
     * @param non-empty-string $key
     */
    public function __construct(
        private readonly string $key
    ) {
    }

    /**
     * @return array<non-empty-string>
     */
    public function get(ArrayAccessor $arrayAccessor, array $data): array
    {
        return $arrayAccessor->getNonEmptyStringArray($this->key, $data);
    }
}
