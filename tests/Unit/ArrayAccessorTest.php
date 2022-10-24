<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Tests\Unit;

use PHPUnit\Framework\TestCase;
use SmartAssert\ServiceClient\ArrayAccessor;

class ArrayAccessorTest extends TestCase
{
    private ArrayAccessor $arrayAccessor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->arrayAccessor = new ArrayAccessor();
    }

    /**
     * @dataProvider getStringDataProvider
     *
     * @param non-empty-string $key
     * @param array<mixed>     $data
     */
    public function testGetString(string $key, array $data, ?string $expected): void
    {
        self::assertSame($expected, $this->arrayAccessor->getString($key, $data));
    }

    /**
     * @return array<mixed>
     */
    public function getStringDataProvider(): array
    {
        return [
            'empty data' => [
                'key' => 'not-relevant',
                'data' => [],
                'expected' => null,
            ],
            'key not present' => [
                'key' => 'not-present',
                'data' => [
                    'present' => 'string value',
                ],
                'expected' => null,
            ],
            'value is not a string' => [
                'key' => 'integer-value-key',
                'data' => [
                    'integer-value-key' => 100,
                ],
                'expected' => null,
            ],
            'value is an empty string' => [
                'key' => 'key',
                'data' => [
                    'key' => '',
                ],
                'expected' => '',
            ],
            'value is a non-empty string' => [
                'key' => 'key',
                'data' => [
                    'key' => 'non-empty string value',
                ],
                'expected' => 'non-empty string value',
            ],
        ];
    }

    /**
     * @dataProvider getIntegerDataProvider
     *
     * @param non-empty-string $key
     * @param array<mixed>     $data
     */
    public function testGetInteger(string $key, array $data, ?int $expected): void
    {
        self::assertSame($expected, $this->arrayAccessor->getInteger($key, $data));
    }

    /**
     * @return array<mixed>
     */
    public function getIntegerDataProvider(): array
    {
        return [
            'empty data' => [
                'key' => 'not-relevant',
                'data' => [],
                'expected' => null,
            ],
            'key not present' => [
                'key' => 'not-present',
                'data' => [
                    'present' => 100,
                ],
                'expected' => null,
            ],
            'value is not an integer' => [
                'key' => 'string-value-key',
                'data' => [
                    'string-value-key' => 'string value',
                ],
                'expected' => null,
            ],
            'value is a negative integer' => [
                'key' => 'key',
                'data' => [
                    'key' => -1,
                ],
                'expected' => -1,
            ],
            'value is a zero' => [
                'key' => 'key',
                'data' => [
                    'key' => 0,
                ],
                'expected' => 0,
            ],
            'value is a positive integer' => [
                'key' => 'key',
                'data' => [
                    'key' => 1,
                ],
                'expected' => 1,
            ],
        ];
    }

    /**
     * @dataProvider getNonEmptyStringDataProvider
     *
     * @param non-empty-string $key
     * @param array<mixed>     $data
     */
    public function testGetNonEmptyString(string $key, array $data, ?string $expected): void
    {
        self::assertSame($expected, $this->arrayAccessor->getNonEmptyString($key, $data));
    }

    /**
     * @return array<mixed>
     */
    public function getNonEmptyStringDataProvider(): array
    {
        return [
            'empty data' => [
                'key' => 'not-relevant',
                'data' => [],
                'expected' => null,
            ],
            'key not present' => [
                'key' => 'not-present',
                'data' => [
                    'present' => 'string value',
                ],
                'expected' => null,
            ],
            'value is not a string' => [
                'key' => 'integer-value-key',
                'data' => [
                    'integer-value-key' => 100,
                ],
                'expected' => null,
            ],
            'value is an empty string' => [
                'key' => 'key',
                'data' => [
                    'key' => '',
                ],
                'expected' => null,
            ],
            'value is a non-empty string' => [
                'key' => 'key',
                'data' => [
                    'key' => 'non-empty string value',
                ],
                'expected' => 'non-empty string value',
            ],
        ];
    }

    /**
     * @dataProvider getNonEmptyStringArrayDataProvider
     *
     * @param array<non-empty-string> $expected
     * @param array<mixed>            $data
     */
    public function testGetNonEmptyStringArray(string $key, array $data, array $expected): void
    {
        self::assertSame($expected, $this->arrayAccessor->getNonEmptyStringArray($key, $data));
    }

    /**
     * @return array<mixed>
     */
    public function getNonEmptyStringArrayDataProvider(): array
    {
        return [
            'empty data' => [
                'key' => 'not-relevant',
                'data' => [],
                'expected' => [],
            ],
            'key not present' => [
                'key' => 'not-present',
                'data' => [
                    'present' => [
                        'one',
                    ],
                ],
                'expected' => [],
            ],
            'no string values present' => [
                'key' => 'key',
                'data' => [
                    'key' => [
                        100,
                        true,
                        null,
                    ],
                ],
                'expected' => [],
            ],
            'no non-empty string values present' => [
                'key' => 'key',
                'data' => [
                    'key' => [
                        '',
                        '  ',
                    ],
                ],
                'expected' => [],
            ],
            'non-empty string values present' => [
                'key' => 'key',
                'data' => [
                    'key' => [
                        true,
                        'one',
                        '',
                        'two',
                        '  ',
                        null,
                        'three with trailing whitespace   '
                    ],
                ],
                'expected' => [
                    'one',
                    'two',
                    'three with trailing whitespace',
                ],
            ],
        ];
    }

    /**
     * @dataProvider getPositiveIntegerDataProvider
     *
     * @param non-empty-string $key
     * @param array<mixed>     $data
     */
    public function testGetPositiveInteger(string $key, array $data, ?int $expected): void
    {
        self::assertSame($expected, $this->arrayAccessor->getPositiveInteger($key, $data));
    }

    /**
     * @return array<mixed>
     */
    public function getPositiveIntegerDataProvider(): array
    {
        return [
            'empty data' => [
                'key' => 'not-relevant',
                'data' => [],
                'expected' => null,
            ],
            'key not present' => [
                'key' => 'not-present',
                'data' => [
                    'present' => 100,
                ],
                'expected' => null,
            ],
            'value is not an integer' => [
                'key' => 'string-value-key',
                'data' => [
                    'string-value-key' => 'string value',
                ],
                'expected' => null,
            ],
            'value is a negative integer' => [
                'key' => 'key',
                'data' => [
                    'key' => -1,
                ],
                'expected' => null,
            ],
            'value is a zero' => [
                'key' => 'key',
                'data' => [
                    'key' => 0,
                ],
                'expected' => null,
            ],
            'value is a positive integer' => [
                'key' => 'key',
                'data' => [
                    'key' => 1,
                ],
                'expected' => 1,
            ],
        ];
    }
}
