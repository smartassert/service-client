<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\RequestFactory;

/**
 * @implements \IteratorAggregate<string, RequestMiddlewareInterface>
 */
class RequestMiddlewareCollection implements \IteratorAggregate
{
    /**
     * @var RequestMiddlewareInterface[]
     */
    private array $requestMiddleware = [];

    public function set(string $key, RequestMiddlewareInterface $middleware): RequestMiddlewareCollection
    {
        $this->requestMiddleware[$key] = $middleware;

        return $this;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->requestMiddleware);
    }
}
