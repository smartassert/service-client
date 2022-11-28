<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Exception;

use Psr\Http\Message\ResponseInterface;

class NonSuccessResponseException extends AbstractInvalidResponseException implements HttpResponseExceptionInterface
{
    public function __construct(
        ResponseInterface $response,
    ) {
        parent::__construct(
            $response,
            sprintf('%s: %s', $response->getStatusCode(), $response->getReasonPhrase()),
        );
    }
}
