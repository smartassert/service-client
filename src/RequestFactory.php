<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient;

use Psr\Http\Message\RequestInterface;
use SmartAssert\SecurityTokenExtractor\TokenExtractor;
use SmartAssert\ServiceClient\Authentication\BearerAuthentication;

class RequestFactory
{
    public function __construct(
        private readonly TokenExtractor $tokenExtractor,
    ) {
    }

    public function createWithAuthenticationFromHttpRequest(
        string $method,
        string $url,
        RequestInterface $httpRequest
    ): Request {
        $request = new Request($method, $url);

        $token = $this->tokenExtractor->extract($httpRequest);
        if (null !== $token) {
            $request = $request->withAuthentication(new BearerAuthentication($token));
        }

        return $request;
    }
}
