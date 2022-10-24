<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\ObjectFactory;

use SmartAssert\ServiceClient\ArrayAccessor;

interface AccessorInterface
{
    /**
     * @param array<mixed> $data
     */
    public function get(ArrayAccessor $arrayAccessor, array $data): mixed;
}
