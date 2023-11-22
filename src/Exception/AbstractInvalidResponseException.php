<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Exception;

use Psr\Http\Message\ResponseInterface;

abstract class AbstractInvalidResponseException extends \Exception implements HttpResponseExceptionInterface
{
    public function __construct(
        private readonly ResponseInterface $httpResponse,
        string $message,
    ) {
        parent::__construct($message, $this->httpResponse->getStatusCode());
    }

    public function getHttpResponse(): ResponseInterface
    {
        return $this->httpResponse;
    }

    public function getStatusCode(): int
    {
        return $this->httpResponse->getStatusCode();
    }
}
