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
                    'application/json',
                    '',
                    new HttpResponse()
                ),
            ],
            'http response has incorrect content type' => [
                'response' => new JsonResponse(
                    new HttpResponse(200, ['content-type' => 'text/plain'])
                ),
                'expected' => new InvalidResponseContentException(
                    'application/json',
                    'text/plain',
                    new HttpResponse()
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

    /**
     * @dataProvider getFromEmptyCollectionDataProvider
     * @dataProvider getKeyMissingDataProvider
     * @dataProvider getStringDataProvider
     *
     * @param non-empty-string $key
     */
    public function testGetString(JsonResponse $response, string $key, ?string $expected): void
    {
        self::assertSame($expected, $response->getString($key));
    }

    /**
     * @return array<mixed>
     */
    public function getStringDataProvider(): array
    {
        return [
            'value is not a string' => [
                'response' => new JsonResponse(
                    new HttpResponse(200, ['content-type' => 'application/json'], '{"key": 100}')
                ),
                'key' => 'key',
                'expected' => null,
            ],
            'value is an empty string' => [
                'response' => new JsonResponse(
                    new HttpResponse(200, ['content-type' => 'application/json'], '{"key": ""}')
                ),
                'key' => 'key',
                'expected' => '',
            ],
            'value is a non-empty string' => [
                'response' => new JsonResponse(
                    new HttpResponse(200, ['content-type' => 'application/json'], '{"key": "non-empty"}')
                ),
                'key' => 'key',
                'expected' => 'non-empty',
            ],
        ];
    }

    /**
     * @dataProvider getFromEmptyCollectionDataProvider
     * @dataProvider getKeyMissingDataProvider
     * @dataProvider getIntegerDataProvider
     *
     * @param non-empty-string $key
     */
    public function testGetInteger(JsonResponse $response, string $key, ?int $expected): void
    {
        self::assertSame($expected, $response->getInteger($key));
    }

    /**
     * @return array<mixed>
     */
    public function getIntegerDataProvider(): array
    {
        return [
            'value is not an integer' => [
                'response' => new JsonResponse(
                    new HttpResponse(200, ['content-type' => 'application/json'], '{"key": "string"}')
                ),
                'key' => 'key',
                'expected' => null,
            ],
            'value is a negative integer' => [
                'response' => new JsonResponse(
                    new HttpResponse(200, ['content-type' => 'application/json'], '{"key": -1}')
                ),
                'key' => 'key',
                'expected' => -1,
            ],
            'value is a zero' => [
                'response' => new JsonResponse(
                    new HttpResponse(200, ['content-type' => 'application/json'], '{"key": 0}')
                ),
                'key' => 'key',
                'expected' => 0,
            ],
            'value is a positive integer' => [
                'response' => new JsonResponse(
                    new HttpResponse(200, ['content-type' => 'application/json'], '{"key": 1}')
                ),
                'key' => 'key',
                'expected' => 1,
            ],
        ];
    }

    /**
     * @dataProvider getFromEmptyCollectionDataProvider
     * @dataProvider getKeyMissingDataProvider
     * @dataProvider getNonEmptyStringDataProvider
     *
     * @param non-empty-string $key
     */
    public function testGetNonEmptyString(JsonResponse $response, string $key, ?string $expected): void
    {
        self::assertSame($expected, $response->getNonEmptyString($key));
    }

    /**
     * @return array<mixed>
     */
    public function getNonEmptyStringDataProvider(): array
    {
        return [
            'value is not a string' => [
                'response' => new JsonResponse(
                    new HttpResponse(200, ['content-type' => 'application/json'], '{"key": 100}')
                ),
                'key' => 'key',
                'expected' => null,
            ],
            'value is an empty string' => [
                'response' => new JsonResponse(
                    new HttpResponse(200, ['content-type' => 'application/json'], '{"key": ""}')
                ),
                'key' => 'key',
                'expected' => null,
            ],
            'value is a non-empty string' => [
                'response' => new JsonResponse(
                    new HttpResponse(200, ['content-type' => 'application/json'], '{"key": "non-empty"}')
                ),
                'key' => 'key',
                'expected' => 'non-empty',
            ],
        ];
    }

    /**
     * @dataProvider getFromEmptyCollectionDataProvider
     * @dataProvider getKeyMissingDataProvider
     *
     * @param non-empty-string        $key
     * @param array<non-empty-string> $expected
     */
    public function testGetNonEmptyStringCollection(JsonResponse $response, string $key, ?array $expected): void
    {
        $expected = is_array($expected) ? $expected : [];

        self::assertSame($expected, $response->getNonEmptyStringCollection($key));
    }

    /**
     * @return array<mixed>
     */
    public function getNonEmptyStringArrayDataProvider(): array
    {
        return [
            'no string values present' => [
                'response' => new JsonResponse(
                    new HttpResponse(
                        200,
                        ['content-type' => 'application/json'],
                        (string) json_encode([
                            'key' => [100, true, null],
                        ])
                    )
                ),
                'key' => 'key',
                'expected' => [],
            ],
            'no non-empty string values present' => [
                'response' => new JsonResponse(
                    new HttpResponse(
                        200,
                        ['content-type' => 'application/json'],
                        (string) json_encode([
                            'key' => ['', '  '],
                        ])
                    )
                ),
                'key' => 'key',
                'expected' => [],
            ],
            'non-empty string values present' => [
                'response' => new JsonResponse(
                    new HttpResponse(
                        200,
                        ['content-type' => 'application/json'],
                        (string) json_encode([
                            'key' => [
                                true,
                                'one',
                                '',
                                'two',
                                '  ',
                                null,
                                'three with trailing whitespace   '
                            ],
                        ])
                    )
                ),
                'key' => 'key',
                'expected' => [
                    'one',
                    'two',
                    'three with trailing whitespace',
                ],
            ],
        ];
    }

    /**
     * @dataProvider getFromEmptyCollectionDataProvider
     * @dataProvider getKeyMissingDataProvider
     * @dataProvider getPositiveIntegerDataProvider
     *
     * @param non-empty-string $key
     */
    public function testGetPositiveInteger(JsonResponse $response, string $key, ?int $expected): void
    {
        self::assertSame($expected, $response->getPositiveInteger($key));
    }

    /**
     * @return array<mixed>
     */
    public function getPositiveIntegerDataProvider(): array
    {
        return [
            'value is not an integer' => [
                'response' => new JsonResponse(
                    new HttpResponse(200, ['content-type' => 'application/json'], '{"key": "string"}')
                ),
                'key' => 'key',
                'expected' => null,
            ],
            'value is a negative integer' => [
                'response' => new JsonResponse(
                    new HttpResponse(200, ['content-type' => 'application/json'], '{"key": -1}')
                ),
                'key' => 'key',
                'expected' => null,
            ],
            'value is a zero' => [
                'response' => new JsonResponse(
                    new HttpResponse(200, ['content-type' => 'application/json'], '{"key": 0}')
                ),
                'key' => 'key',
                'expected' => null,
            ],
            'value is a positive integer' => [
                'response' => new JsonResponse(
                    new HttpResponse(200, ['content-type' => 'application/json'], '{"key": 1}')
                ),
                'key' => 'key',
                'expected' => 1,
            ],
        ];
    }

    /**
     * @return array<mixed>
     */
    public function getFromEmptyCollectionDataProvider(): array
    {
        return [
            'empty array' => [
                'response' => new JsonResponse(
                    new HttpResponse(200, ['content-type' => 'application/json'], '[]')
                ),
                'key' => 'key',
                'expected' => null,
            ],
            'empty object' => [
                'response' => new JsonResponse(
                    new HttpResponse(200, ['content-type' => 'application/json'], '{}')
                ),
                'key' => 'key',
                'expected' => null,
            ],
        ];
    }

    /**
     * @return array<mixed>
     */
    public function getKeyMissingDataProvider(): array
    {
        return [
            'key not present' => [
                'response' => new JsonResponse(
                    new HttpResponse(200, ['content-type' => 'application/json'], '{"key": "value"}')
                ),
                'key' => 'missing',
                'expected' => null,
            ],
        ];
    }
}
