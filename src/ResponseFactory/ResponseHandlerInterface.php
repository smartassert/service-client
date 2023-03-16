<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\ResponseFactory;

use Psr\Http\Message\ResponseInterface as HttpResponseInterface;
use SmartAssert\ServiceClient\Response\ResponseInterface;

interface ResponseHandlerInterface
{
    public function handles(string $contentType): bool;

    public function create(HttpResponseInterface $httpResponse): ResponseInterface;
}
