<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\ObjectFactory;

use SmartAssert\ServiceClient\ArrayAccessor;
use SmartAssert\ServiceClient\Exception\InvalidObjectDataException;

class ObjectFactory
{
    public function __construct(
        private readonly ArrayAccessor $arrayAccessor,
    ) {
    }

    /**
     * @param array<mixed> $data
     *
     * @throws InvalidObjectDataException
     */
    public function create(ObjectDefinitionInterface $objectDefinition, array $data): object
    {
        $values = [];

        foreach ($objectDefinition->getAccessors() as $accessor) {
            $values[] = $accessor->get($this->arrayAccessor, $data);
        }

        if ($objectDefinition->isValid($values)) {
            return $objectDefinition->create($values);
        }

        throw new InvalidObjectDataException($data, $objectDefinition);
    }
}
