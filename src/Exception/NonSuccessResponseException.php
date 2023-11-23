<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Exception;

use SmartAssert\ServiceClient\Response\ResponseInterface;

class NonSuccessResponseException extends AbstractInvalidResponseException implements ResponseExceptionInterface
{
    public function __construct(
        private readonly ResponseInterface $response,
    ) {
        parent::__construct(
            $response->getHttpResponse(),
            sprintf('%s: %s', $response->getStatusCode(), $response->getHttpResponse()->getReasonPhrase()),
        );
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
