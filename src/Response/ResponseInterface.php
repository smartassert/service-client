<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Response;

use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

interface ResponseInterface
{
    public function isSuccessful(): bool;

    public function getHttpResponse(): HttpResponseInterface;
}
