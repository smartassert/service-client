<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Authentication;

class BearerAuthentication extends Authentication
{
    public function __construct(
        string $token,
    ) {
        parent::__construct('Bearer ' . $token);
    }
}
