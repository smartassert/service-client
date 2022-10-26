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
