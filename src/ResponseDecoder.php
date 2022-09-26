<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient;

use Psr\Http\Message\ResponseInterface;
use SmartAssert\ServiceClient\Exception\InvalidResponseContentException;
use SmartAssert\ServiceClient\Exception\InvalidResponseDataException;

class ResponseDecoder
{
    /**
     * @return array<mixed>
     *
     * @throws InvalidResponseContentException
     * @throws InvalidResponseDataException
     */
    public function decodedJsonResponse(ResponseInterface $response): array
    {
        $expectedContentType = 'application/json';
        $actualContentType = $response->getHeaderLine('content-type');

        if ($expectedContentType !== $actualContentType) {
            throw new InvalidResponseContentException($expectedContentType, $actualContentType, $response);
        }

        $data = json_decode($response->getBody()->getContents(), true);
        if (!is_array($data)) {
            throw new InvalidResponseDataException('array', gettype($data), $response);
        }

        return $data;
    }
}
