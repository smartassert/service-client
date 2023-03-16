<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\ResponseFactory;

use Psr\Http\Message\ResponseInterface as HttpResponseInterface;
use SmartAssert\ServiceClient\Response\JsonResponse;
use SmartAssert\ServiceClient\Response\ResponseInterface;

class JsonResponseHandler implements ResponseHandlerInterface
{
    public function handles(string $contentType): bool
    {
        return 'application/json' === $contentType;
    }

    public function create(HttpResponseInterface $httpResponse): ResponseInterface
    {
        return new JsonResponse($httpResponse);
    }
}
