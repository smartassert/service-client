<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Exception;

use Psr\Http\Message\ResponseInterface;
use SmartAssert\ServiceClient\Exception\HttpResponseExceptionInterface as HttpResponseException;
use SmartAssert\ServiceClient\Exception\HttpResponsePayloadExceptionInterface as HttpResponsePayloadException;
use SmartAssert\ServiceClient\Response\JsonResponse;

class InvalidModelDataException extends \Exception implements HttpResponsePayloadException, HttpResponseException
{
    /**
     * @param class-string $class
     * @param array<mixed> $payload
     */
    public function __construct(
        public readonly string $class,
        public readonly ResponseInterface $response,
        public readonly array $payload,
    ) {
        parent::__construct(sprintf('Data in response invalid for creating an instance of "%s"', $class));
    }

    /**
     * @param class-string $class
     *
     * @throws InvalidResponseContentException
     * @throws InvalidResponseDataException
     */
    public static function fromJsonResponse(string $class, JsonResponse $response): InvalidModelDataException
    {
        return new InvalidModelDataException($class, $response->getHttpResponse(), $response->getData());
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }
}
