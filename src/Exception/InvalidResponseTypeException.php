<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Exception;

use Psr\Http\Message\ResponseInterface;

class InvalidResponseTypeException extends AbstractInvalidResponseContentException
{
    /**
     * @param class-string $expected
     * @param class-string $actual
     */
    public function __construct(ResponseInterface $response, string $expected, string $actual)
    {
        parent::__construct($response, 'response type', $expected, $actual);
    }
}
