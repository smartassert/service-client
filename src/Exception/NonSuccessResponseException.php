<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Exception;

use Psr\Http\Message\ResponseInterface;

class NonSuccessResponseException extends \Exception implements HttpResponseExceptionInterface
{
    public function __construct(
        public readonly ResponseInterface $response,
    ) {
        parent::__construct(
            sprintf('%s: %s', $this->response->getStatusCode(), $this->response->getReasonPhrase()),
            $this->response->getStatusCode()
        );
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
