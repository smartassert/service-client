<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Exception;

use Psr\Http\Message\ResponseInterface;

interface HttpResponseExceptionInterface extends \Throwable
{
    public function getResponse(): ResponseInterface;
}
