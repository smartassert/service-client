<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\RequestFactory;

use SmartAssert\ServiceClient\Authentication\Authentication;
use SmartAssert\ServiceClient\Request;

class AuthenticationMiddleware implements RequestMiddlewareInterface
{
    private Authentication $authentication;

    public function setAuthentication(Authentication $authentication): self
    {
        $this->authentication = $authentication;

        return $this;
    }

    public function process(Request $request): Request
    {
        if (!isset($this->authentication)) {
            return $request;
        }

        return $request->withAuthentication($this->authentication);
    }
}
