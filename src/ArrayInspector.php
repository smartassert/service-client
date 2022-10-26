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
     * @param non-empty-string     $type
     */
    public function has(int|string $key, string $type): bool
    {
        return gettype($this->data[$key] ?? null) === $type;
    }

    /**
     * @param callable(int|non-empty-string $key, mixed $value): mixed $action
     * @param null|callable(mixed $item): bool $validator
     *
     * @return array<mixed>
     */
    public function each(callable $action, ?callable $validator = null): array
    {
        $items = [];

        foreach ($this->data as $key => $value) {
            $item = $action($key, $value);

            if (null === $validator || (is_callable($validator) && true === $validator($item))) {
                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * @param int|non-empty-string $key
     *
     * @return array<mixed>
     */
    public function getArray(int|string $key): Array
    {
        $value = $this->data[$key] ?? [];

        return is_array($value) ? $value : [];
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
     *
     * @return null|non-empty-string
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
