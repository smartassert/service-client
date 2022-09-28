<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Payload;

class JsonPayload extends Payload
{
    /**
     * @param array<mixed>|\JsonSerializable $data
     */
    public function __construct(array|\JsonSerializable $data)
    {
        parent::__construct('application/json', (string) json_encode($data));
    }
}
