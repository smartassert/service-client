<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient;

class RequestFactory
{
    /**
     * @param non-empty-string $method
     */
    public function create(string $method, string $url): Request
    {
        return new Request($method, $url);
    }
}
