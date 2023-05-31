<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Exception;

use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestInterface;

interface CurlExceptionInterface extends NetworkExceptionInterface
{
    public function getRequest(): RequestInterface;

    public function getCurlCode(): int;

    public function getCurlMessage(): string;
}
