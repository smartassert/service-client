<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Exception;

use Psr\Http\Message\ResponseInterface as HttpResponseInterface;
use SmartAssert\ServiceClient\Response\ResponseInterface;

class InvalidResponseTypeException extends AbstractInvalidResponseContentException
{
    /**
     * @param class-string $expected
     * @param class-string $actual
     */
    public function __construct(HttpResponseInterface $response, string $expected, string $actual)
    {
        parent::__construct($response, 'response type', $expected, $actual);
    }

    /**
     * @param class-string $expected
     */
    public static function create(ResponseInterface $response, string $expected): InvalidResponseTypeException
    {
        return new InvalidResponseTypeException($response->getHttpResponse(), $expected, $response::class);
    }
}
