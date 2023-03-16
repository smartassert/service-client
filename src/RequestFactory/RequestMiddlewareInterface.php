<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\RequestFactory;

use SmartAssert\ServiceClient\Request;

interface RequestMiddlewareInterface
{
    public function process(Request $request): Request;
}
