<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Tests\Unit;

use PHPUnit\Framework\TestCase;
use SmartAssert\ServiceClient\ArrayInspector;

class ArrayInspectorTest extends TestCase
{
    /**
     * @dataProvider getFromEmptyCollectionDataProvider
     * @dataProvider getKeyMissingDataProvider
     * @dataProvider getStringDataProvider
     *
     * @param non-empty-string $key
     */
    public function testGetString(ArrayInspector $inspector, string $key, ?string $expected): void
    {
        self::assertSame($expected, $inspector->getString($key));
    }

    /**
     * @return array<mixed>
     */
    public function getStringDataProvider(): array
    {
        return [
            'value is not a string' => [
                'inspector' => new ArrayInspector(['key' => 100]),
                'key' => 'key',
                'expected' => null,
            ],
            'value is an empty string' => [
                'inspector' => new ArrayInspector(['key' => '']),
                'key' => 'key',
                'expected' => '',
            ],
            'value is a non-empty string' => [
                'inspector' => new ArrayInspector(['key' => 'non-empty']),
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
    public function testGetInteger(ArrayInspector $inspector, string $key, ?int $expected): void
    {
        self::assertSame($expected, $inspector->getInteger($key));
    }

    /**
     * @return array<mixed>
     */
    public function getIntegerDataProvider(): array
    {
        return [
            'value is not an integer' => [
                'inspector' => new ArrayInspector(['key' => 'string']),
                'key' => 'key',
                'expected' => null,
            ],
            'value is a negative integer' => [
                'inspector' => new ArrayInspector(['key' => -1]),
                'key' => 'key',
                'expected' => -1,
            ],
            'value is a zero' => [
                'inspector' => new ArrayInspector(['key' => 0]),
                'key' => 'key',
                'expected' => 0,
            ],
            'value is a positive integer' => [
                'inspector' => new ArrayInspector(['key' => 1]),
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
    public function testGetNonEmptyString(ArrayInspector $inspector, string $key, ?string $expected): void
    {
        self::assertSame($expected, $inspector->getNonEmptyString($key));
    }

    /**
     * @return array<mixed>
     */
    public function getNonEmptyStringDataProvider(): array
    {
        return [
            'value is not a string' => [
                'inspector' => new ArrayInspector(['key' => 100]),
                'key' => 'key',
                'expected' => null,
            ],
            'value is an empty string' => [
                'inspector' => new ArrayInspector(['key' => '']),
                'key' => 'key',
                'expected' => null,
            ],
            'value is a non-empty string' => [
                'inspector' => new ArrayInspector(['key' => 'non-empty']),
                'key' => 'key',
                'expected' => 'non-empty',
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
    public function testGetPositiveInteger(ArrayInspector $inspector, string $key, ?int $expected): void
    {
        self::assertSame($expected, $inspector->getPositiveInteger($key));
    }

    /**
     * @return array<mixed>
     */
    public function getPositiveIntegerDataProvider(): array
    {
        return [
            'value is not an integer' => [
                'inspector' => new ArrayInspector(['key' => 'string']),
                'key' => 'key',
                'expected' => null,
            ],
            'value is a negative integer' => [
                'inspector' => new ArrayInspector(['key' => -1]),
                'key' => 'key',
                'expected' => null,
            ],
            'value is a zero' => [
                'inspector' => new ArrayInspector(['key' => 0]),
                'key' => 'key',
                'expected' => null,
            ],
            'value is a positive integer' => [
                'inspector' => new ArrayInspector(['key' => 1]),
                'key' => 'key',
                'expected' => 1,
            ],
        ];
    }

    /**
     * @dataProvider getArrayInspectorDataProvider
     *
     * @param non-empty-string $key
     */
    public function testGetArrayInspector(ArrayInspector $inspector, string $key, ArrayInspector $expected): void
    {
        self::assertEquals($expected, $inspector->getArrayInspector($key));
    }

    /**
     * @return array<mixed>
     */
    public function getArrayInspectorDataProvider(): array
    {
        return [
            'empty array' => [
                'inspector' => new ArrayInspector([]),
                'key' => 'key',
                'expected' => new ArrayInspector([]),
            ],
            'key not present' => [
                'inspector' => new ArrayInspector(['key' => 'value']),
                'key' => 'missing',
                'expected' => new ArrayInspector([]),
            ],
            'value is not an array' => [
                'inspector' => new ArrayInspector(['key' => 'string']),
                'key' => 'key',
                'expected' => new ArrayInspector([]),
            ],
            'value is an empty array' => [
                'inspector' => new ArrayInspector(['key' => []]),
                'key' => 'key',
                'expected' => new ArrayInspector([]),
            ],
            'value is a non-empty array' => [
                'inspector' => new ArrayInspector(['key' => [
                    'key1' => 'value1',
                    'key2' => 'value2',
                    'key3' => 'value3',
                ]]),
                'key' => 'key',
                'expected' => new ArrayInspector([
                    'key1' => 'value1',
                    'key2' => 'value2',
                    'key3' => 'value3',
                ]),
            ],
        ];
    }

    /**
     * @dataProvider hasDataProvider
     *
     * @param non-empty-string $key
     * @param non-empty-string $type
     */
    public function testHas(ArrayInspector $inspector, string $key, string $type, bool $expected): void
    {
        self::assertSame($expected, $inspector->has($key, $type));
    }

    /**
     * @return array<mixed>
     */
    public function hasDataProvider(): array
    {
        return [
            'empty' => [
                'inspector' => new ArrayInspector([]),
                'key' => 'key',
                'type' => 'string',
                'expected' => false,
            ],
            'key not present' => [
                'inspector' => new ArrayInspector([
                    'key' => 'value',
                ]),
                'key' => 'not-present',
                'type' => 'string',
                'expected' => false,
            ],
            'incorrect type' => [
                'inspector' => new ArrayInspector([
                    'key' => 'value',
                ]),
                'key' => 'key',
                'type' => 'integer',
                'expected' => false,
            ],
            'integer value present' => [
                'inspector' => new ArrayInspector([
                    'key' => 100,
                ]),
                'key' => 'key',
                'type' => 'integer',
                'expected' => true,
            ],
            'double (float) value present' => [
                'inspector' => new ArrayInspector([
                    'key' => M_PI,
                ]),
                'key' => 'key',
                'type' => 'double',
                'expected' => true,
            ],
            'string value present' => [
                'inspector' => new ArrayInspector([
                    'key' => 'value',
                ]),
                'key' => 'key',
                'type' => 'string',
                'expected' => true,
            ],
            'array value present' => [
                'inspector' => new ArrayInspector([
                    'key' => [],
                ]),
                'key' => 'key',
                'type' => 'array',
                'expected' => true,
            ],
            'null value present' => [
                'inspector' => new ArrayInspector([
                    'key' => null,
                ]),
                'key' => 'key',
                'type' => 'NULL',
                'expected' => true,
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
                'inspector' => new ArrayInspector([]),
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
                'inspector' => new ArrayInspector(['key' => 'value']),
                'key' => 'missing',
                'expected' => null,
            ],
        ];
    }
}
