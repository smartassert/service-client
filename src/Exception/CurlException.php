<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Exception;

use Psr\Http\Message\RequestInterface;

class CurlException extends \Exception implements CurlExceptionInterface
{
    public function __construct(
        private readonly RequestInterface $request,
        private readonly int $curlCode = 0,
        private readonly string $curlMessage = '',
        \Throwable $previous = null
    ) {
        parent::__construct($curlMessage, $curlCode, $previous);
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getCurlCode(): int
    {
        return $this->curlCode;
    }

    public function getCurlMessage(): string
    {
        return $this->curlMessage;
    }
}
