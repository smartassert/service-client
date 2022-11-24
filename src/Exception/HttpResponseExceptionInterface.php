<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Exception;

use Psr\Http\Message\ResponseInterface;

interface HttpResponseExceptionInterface
{
    public function getResponse(): ResponseInterface;
}
