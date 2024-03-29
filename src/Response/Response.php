<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Response;

use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

class Response implements ResponseInterface
{
    public function __construct(
        private readonly HttpResponseInterface $httpResponse,
    ) {
    }

    public function isSuccessful(): bool
    {
        return $this->httpResponse->getStatusCode() < 300;
    }

    public function getHttpResponse(): HttpResponseInterface
    {
        return $this->httpResponse;
    }

    public function getStatusCode(): int
    {
        return $this->getHttpResponse()->getStatusCode();
    }
}
