<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient;

use SmartAssert\ServiceClient\Authentication\Authentication;
use SmartAssert\ServiceClient\Payload\Payload;

class Request
{
    private ?Authentication $authentication = null;
    private ?Payload $payload = null;

    /**
     * @param non-empty-string $method
     */
    public function __construct(
        public readonly string $method,
        public readonly string $url,
    ) {
    }

    public function getAuthentication(): ?Authentication
    {
        return $this->authentication;
    }

    public function withAuthentication(Authentication $authentication): Request
    {
        $new = clone $this;
        $new->authentication = $authentication;

        return $new;
    }

    public function getPayload(): ?Payload
    {
        return $this->payload;
    }

    public function withPayload(Payload $payload): Request
    {
        $new = clone $this;
        $new->payload = $payload;

        return $new;
    }
}
