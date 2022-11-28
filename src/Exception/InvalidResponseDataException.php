<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Exception;

use Psr\Http\Message\ResponseInterface;

class InvalidResponseDataException extends AbstractInvalidResponseContentException
{
    public function __construct(string $expected, string $actual, ResponseInterface $response)
    {
        parent::__construct($response, 'payload data type', $expected, $actual);
    }
}
