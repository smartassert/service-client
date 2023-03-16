<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\RequestFactory;

use SmartAssert\ServiceClient\Request;

class RequestFactory
{
    private readonly RequestMiddlewareCollection $requestMiddlewareCollection;

    public function __construct(?RequestMiddlewareCollection $requestMiddlewareCollection = null)
    {
        $this->requestMiddlewareCollection = $requestMiddlewareCollection instanceof RequestMiddlewareCollection
            ? $requestMiddlewareCollection
            : new RequestMiddlewareCollection();
    }

    public function getRequestMiddlewareCollection(): RequestMiddlewareCollection
    {
        return $this->requestMiddlewareCollection;
    }

    /**
     * @param non-empty-string $method
     */
    public function create(string $method, string $url): Request
    {
        $request = new Request($method, $url);

        foreach ($this->requestMiddlewareCollection as $middleware) {
            $request = $middleware->process($request);
        }

        return $request;
    }
}
