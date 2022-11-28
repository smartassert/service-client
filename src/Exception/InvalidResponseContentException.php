<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Exception;

use Psr\Http\Message\ResponseInterface;

class InvalidResponseContentException extends AbstractInvalidResponseContentException
{
    public function __construct(ResponseInterface $response, string $expected, string $actual)
    {
        parent::__construct($response, 'content-type header', $expected, $actual);
    }
}
