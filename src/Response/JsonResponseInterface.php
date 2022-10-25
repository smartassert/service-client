<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Response;

interface JsonResponseInterface extends ResponseInterface
{
    /**
     * @return array<mixed>
     */
    public function getData(): array;

    /**
     * @param non-empty-string $key
     */
    public function getString(string $key): ?string;

    /**
     * @param non-empty-string $key
     */
    public function getInteger(string $key): ?int;

    /**
     * @param non-empty-string $key
     *
     * @return null|non-empty-string
     */
    public function getNonEmptyString(string $key): ?string;

    /**
     * @param non-empty-string $key
     *
     * @return non-empty-string[]
     */
    public function getNonEmptyStringCollection(string $key): array;

    /**
     * @param non-empty-string $key
     *
     * @return null|positive-int
     */
    public function getPositiveInteger(string $key): ?int;
}
