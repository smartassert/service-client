<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Exception;

use SmartAssert\ServiceClient\ObjectFactory\ObjectDefinitionInterface;

class InvalidObjectDataException extends \Exception
{
    /**
     * @param array<mixed> $data
     */
    public function __construct(
        public readonly array $data,
        public readonly ObjectDefinitionInterface $objectDefinition,
    ) {
        parent::__construct();
    }
}
