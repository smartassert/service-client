<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient;

class ArrayInspector
{
    /**
     * @param array<mixed> $data
     */
    public function __construct(
        private readonly array $data,
    ) {
    }

    /**
     * @param int|non-empty-string $key
     */
    public function getArrayInspector(int|string $key): ArrayInspector
    {
        $value = $this->data[$key] ?? [];
        $value = is_array($value) ? $value : [];

        return new ArrayInspector($value);
    }

    /**
     * @param int|non-empty-string $key
     */
    public function getString(int|string $key): ?string
    {
        $value = $this->data[$key] ?? null;

        return is_string($value) ? $value : null;
    }

    /**
     * @param int|non-empty-string $key
     */
    public function getInteger(int|string $key): ?int
    {
        $value = $this->data[$key] ?? null;

        return is_int($value) ? $value : null;
    }

    /**
     * @param int|non-empty-string $key
     */
    public function getNonEmptyString(int|string $key): ?string
    {
        return $this->createNonEmptyString(trim((string) $this->getString($key)));
    }

//    public function getNonEmptyStringCollection(string $key): array
//    {
//        $data = $this->getResponseDataAsArray();
//
//        $values = [];
//
//        $unfilteredValues = $data[$key] ?? [];
//        $unfilteredValues = is_array($unfilteredValues) ? $unfilteredValues : [];
//
//        foreach ($unfilteredValues as $unfilteredValue) {
//            if (is_string($unfilteredValue)) {
//                $filteredValue = $this->createNonEmptyString($unfilteredValue);
//
//                if (is_string($filteredValue)) {
//                    $values[] = $filteredValue;
//                }
//            }
//        }
//
//        return $values;
//    }
//

    /**
     * @param int|non-empty-string $key
     */
    public function getPositiveInteger(int|string $key): ?int
    {
        $value = $this->getInteger($key);

        return is_int($value) && $value > 0 ? $value : null;
    }

    /**
     * @return ?non-empty-string
     */
    private function createNonEmptyString(string $value): ?string
    {
        $value = trim($value);

        return '' === $value ? null : $value;
    }
}
