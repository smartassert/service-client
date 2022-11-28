<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Tests\Unit\Response;

use GuzzleHttp\Psr7\Response as HttpResponse;
use PHPUnit\Framework\TestCase;
use SmartAssert\ServiceClient\Exception\InvalidResponseContentException;
use SmartAssert\ServiceClient\Exception\InvalidResponseDataException;
use SmartAssert\ServiceClient\Response\JsonResponse;

class JsonResponseTest extends TestCase
{
    /**
     * @dataProvider getDataThrowsExceptionDataProvider
     */
    public function testGetDataThrowsException(JsonResponse $response, \Exception $expected): void
    {
        $this->expectExceptionObject($expected);

        $response->getData();
    }

    /**
     * @return array<mixed>
     */
    public function getDataThrowsExceptionDataProvider(): array
    {
        return [
            'http response has no content type' => [
                'response' => new JsonResponse(
                    new HttpResponse()
                ),
                'expected' => new InvalidResponseContentException(
                    new HttpResponse(),
                    'application/json',
                    ''
                ),
            ],
            'http response has incorrect content type' => [
                'response' => new JsonResponse(
                    new HttpResponse(200, ['content-type' => 'text/plain'])
                ),
                'expected' => new InvalidResponseContentException(
                    new HttpResponse(),
                    'application/json',
                    'text/plain'
                ),
            ],
            'http response body is not json' => [
                'response' => new JsonResponse(
                    new HttpResponse(200, ['content-type' => 'application/json'], '{!')
                ),
                'expected' => new InvalidResponseDataException(
                    'array',
                    'NULL',
                    new HttpResponse()
                ),
            ],
            'http response body is not an array' => [
                'response' => new JsonResponse(
                    new HttpResponse(200, ['content-type' => 'application/json'], '"string"')
                ),
                'expected' => new InvalidResponseDataException(
                    'array',
                    'string',
                    new HttpResponse()
                ),
            ],
        ];
    }

    /**
     * @dataProvider getDataDataProvider
     *
     * @param array<mixed> $expected
     */
    public function testGetDataSuccess(JsonResponse $response, array $expected): void
    {
        self::assertEquals($expected, $response->getData());
    }

    /**
     * @return array<mixed>
     */
    public function getDataDataProvider(): array
    {
        return [
            'empty json array' => [
                'response' => new JsonResponse(
                    new HttpResponse(200, ['content-type' => 'application/json'], '[]')
                ),
                'expected' => [],
            ],
            'empty json object' => [
                'response' => new JsonResponse(
                    new HttpResponse(200, ['content-type' => 'application/json'], '{}')
                ),
                'expected' => [],
            ],
            'non-empty json array' => [
                'response' => new JsonResponse(
                    new HttpResponse(200, ['content-type' => 'application/json'], '["one", "two", 3]')
                ),
                'expected' => [
                    'one',
                    'two',
                    3,
                ],
            ],
            'non-empty json object' => [
                'response' => new JsonResponse(
                    new HttpResponse(200, ['content-type' => 'application/json'], '{"key1": "value1", "key2": 2}')
                ),
                'expected' => [
                    'key1' => 'value1',
                    'key2' => 2,
                ],
            ],
        ];
    }
}
