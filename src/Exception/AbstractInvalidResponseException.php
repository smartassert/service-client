<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Exception;

use Psr\Http\Message\ResponseInterface;

abstract class AbstractInvalidResponseException extends \Exception implements HttpResponseExceptionInterface
{
    public function __construct(
        public readonly ResponseInterface $response,
        string $message,
    ) {
        parent::__construct($message, $this->response->getStatusCode());
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
