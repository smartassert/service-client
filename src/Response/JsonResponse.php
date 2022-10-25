<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Response;

use SmartAssert\ServiceClient\Exception\InvalidResponseContentException;
use SmartAssert\ServiceClient\Exception\InvalidResponseDataException;

class JsonResponse extends Response implements JsonResponseInterface
{
    /**
     * @var array<mixed>
     */
    private array $data;

    /**
     * @return array<mixed>
     *
     * @throws InvalidResponseContentException
     * @throws InvalidResponseDataException
     */
    public function getData(): array
    {
        if (!isset($this->data)) {
            $this->data = $this->getResponseDataAsArray();
        }

        return $this->data;
    }

    /**
     * @throws InvalidResponseContentException
     * @throws InvalidResponseDataException
     */
    public function getString(string $key): ?string
    {
        $data = $this->getResponseDataAsArray();

        $value = $data[$key] ?? null;

        return is_string($value) ? $value : null;
    }

    /**
     * @throws InvalidResponseContentException
     * @throws InvalidResponseDataException
     */
    public function getInteger(string $key): ?int
    {
        $data = $this->getResponseDataAsArray();

        $value = $data[$key] ?? null;

        return is_int($value) ? $value : null;
    }

    /**
     * @throws InvalidResponseContentException
     * @throws InvalidResponseDataException
     */
    public function getNonEmptyString(string $key): ?string
    {
        return $this->createNonEmptyString(trim((string) $this->getString($key)));
    }

    /**
     * @throws InvalidResponseContentException
     * @throws InvalidResponseDataException
     */
    public function getNonEmptyStringCollection(string $key): array
    {
        $data = $this->getResponseDataAsArray();

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
     * @throws InvalidResponseContentException
     * @throws InvalidResponseDataException
     */
    public function getPositiveInteger(string $key): ?int
    {
        $value = $this->getInteger($key);

        return is_int($value) && $value > 0 ? $value : null;
    }

    /**
     * @return array<mixed>
     *
     * @throws InvalidResponseContentException
     * @throws InvalidResponseDataException
     */
    private function getResponseDataAsArray(): array
    {
        $httpResponse = $this->getHttpResponse();

        $expectedContentType = 'application/json';
        $actualContentType = $httpResponse->getHeaderLine('content-type');

        if ($expectedContentType !== $actualContentType) {
            throw new InvalidResponseContentException($expectedContentType, $actualContentType, $httpResponse);
        }

        $data = json_decode($httpResponse->getBody()->getContents(), true);
        if (!is_array($data)) {
            throw new InvalidResponseDataException('array', gettype($data), $httpResponse);
        }

        return $data;
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
