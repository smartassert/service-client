<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Response;

use SmartAssert\ServiceClient\Exception\InvalidResponseDataException;

class JsonResponse extends Response
{
    /**
     * @var array<mixed>
     */
    private array $data;

    /**
     * @return array<mixed>
     *
     * @throws InvalidResponseDataException
     */
    public function getData(): array
    {
        if (!isset($this->data)) {
            $this->data = $this->getResponseDataAsArray();
        }

        return $this->data;
    }

    /**
     * @return array<mixed>
     *
     * @throws InvalidResponseDataException
     */
    private function getResponseDataAsArray(): array
    {
        $httpResponse = $this->getHttpResponse();

        $data = json_decode($httpResponse->getBody()->getContents(), true);
        if (!is_array($data)) {
            throw new InvalidResponseDataException('array', gettype($data), $httpResponse);
        }

        return $data;
    }
}
