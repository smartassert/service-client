<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient;

class ArrayAccessor
{
    /**
     * @param non-empty-string $key
     * @param array<mixed>     $data
     */
    public function getString(string $key, array $data): ?string
    {
        $value = $data[$key] ?? null;

        return is_string($value) ? $value : null;
    }

    /**
     * @param non-empty-string $key
     * @param array<mixed>     $data
     */
    public function getInteger(string $key, array $data): ?int
    {
        $value = $data[$key] ?? null;

        return is_int($value) ? $value : null;
    }

    /**
     * @param non-empty-string $key
     * @param array<mixed>     $data
     *
     * @return null|non-empty-string
     */
    public function getNonEmptyString(string $key, array $data): ?string
    {
        return $this->createNonEmptyString(trim((string) $this->getString($key, $data)));
    }

    /**
     * @param array<mixed> $data
     *
     * @return non-empty-string[]
     */
    public function getNonEmptyStringArray(string $key, array $data): array
    {
        $values = [];

        $unfilteredValues = $data[$key] ?? [];
        $unfilteredValues = is_array($unfilteredValues) ? $unfilteredValues : [];

        foreach ($unfilteredValues as $unfilteredValue) {
            if (is_string($unfilteredValue)) {
                $filteredValue = $this->createNonEmptyString($unfilteredValue);

                if (is_string($filteredValue)) {
                    $values[] = $filteredValue;
                }
            }
        }

        return $values;
    }

    /**
     * @param non-empty-string$key
     * @param array<mixed> $data
     *
     * @return null|positive-int
     */
    public function getPositiveInteger(string $key, array $data): ?int
    {
        $value = $this->getInteger($key, $data);

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