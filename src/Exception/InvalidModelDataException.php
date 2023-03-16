<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Exception;

use Psr\Http\Message\ResponseInterface;
use SmartAssert\ServiceClient\Exception\AbstractInvalidResponseException as InvalidResponseException;
use SmartAssert\ServiceClient\Response\JsonResponse;

class InvalidModelDataException extends InvalidResponseException implements HttpResponsePayloadExceptionInterface
{
    /**
     * @param class-string $class
     * @param array<mixed> $payload
     */
    public function __construct(
        ResponseInterface $response,
        public readonly string $class,
        public readonly array $payload,
    ) {
        parent::__construct(
            $response,
            sprintf('Data in response invalid for creating an instance of "%s"', $class)
        );
    }

    /**
     * @param class-string $class
     *
     * @throws InvalidResponseDataException
     */
    public static function fromJsonResponse(string $class, JsonResponse $response): InvalidModelDataException
    {
        return new InvalidModelDataException($response->getHttpResponse(), $class, $response->getData());
    }

    public function getPayload(): array
    {
        return $this->payload;
    }
}
