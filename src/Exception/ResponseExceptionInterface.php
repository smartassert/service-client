<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Exception;

use SmartAssert\ServiceClient\Response\ResponseInterface;

interface ResponseExceptionInterface extends \Throwable
{
    public function getResponse(): ResponseInterface;

    public function getStatusCode(): int;
}
