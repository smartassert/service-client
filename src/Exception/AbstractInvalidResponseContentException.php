<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Exception;

use Psr\Http\Message\ResponseInterface;

abstract class AbstractInvalidResponseContentException extends AbstractInvalidResponseException
{
    public function __construct(
        ResponseInterface $response,
        public readonly string $context,
        public readonly string $expected,
        public readonly string $actual,
    ) {
        parent::__construct(
            $response,
            sprintf('Expected %s of "%s", got "%s"', $context, $expected, $actual)
        );
    }
}
