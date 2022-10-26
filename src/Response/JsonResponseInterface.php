<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Response;

interface JsonResponseInterface extends ResponseInterface
{
    /**
     * @return array<mixed>
     */
    public function getData(): array;
}
