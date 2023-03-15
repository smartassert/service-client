<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Exception;

interface HttpResponsePayloadExceptionInterface extends HttpResponseExceptionInterface
{
    /**
     * @return array<mixed>
     */
    public function getPayload(): array;
}
