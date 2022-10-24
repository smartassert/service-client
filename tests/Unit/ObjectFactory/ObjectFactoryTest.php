<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Tests\Unit\ObjectFactory;

use PHPUnit\Framework\TestCase;
use SmartAssert\ServiceClient\ArrayAccessor;
use SmartAssert\ServiceClient\Exception\InvalidObjectDataException;
use SmartAssert\ServiceClient\ObjectFactory\ObjectDefinitionInterface;
use SmartAssert\ServiceClient\ObjectFactory\ObjectFactory;
use SmartAssert\ServiceClient\Tests\Impl\DoubleNonEmptyStringObject;
use SmartAssert\ServiceClient\Tests\Impl\DoubleNonEmptyStringObjectDefinition;
use SmartAssert\ServiceClient\Tests\Impl\SingleStringObject;
use SmartAssert\ServiceClient\Tests\Impl\SingleStringObjectDefinition;

class ObjectFactoryTest extends TestCase
{
    private ObjectFactory $objectFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->objectFactory = new ObjectFactory(
            new ArrayAccessor(),
        );
    }

    /**
     * @dataProvider createThrowsInvalidObjectDataExceptionDataProvider
     *
     * @param array<mixed> $data
     */
    public function testCreateThrowsInvalidObjectDataException(
        ObjectDefinitionInterface $objectDefinition,
        array $data
    ): void {
        self::expectException(InvalidObjectDataException::class);

        $this->objectFactory->create($objectDefinition, $data);
    }

    /**
     * @return array<mixed>
     */
    public function createThrowsInvalidObjectDataExceptionDataProvider(): array
    {
        return [
            'single string object, empty data' => [
                'objectDefinition' => new SingleStringObjectDefinition('key'),
                'data' => [],
            ],
            'single string object, key not present' => [
                'objectDefinition' => new SingleStringObjectDefinition('key1'),
                'data' => [
                    'key2' => 'non-relevant value',
                ],
            ],
            'single string object, type invalid' => [
                'objectDefinition' => new SingleStringObjectDefinition('key'),
                'data' => [
                    'key' => 100,
                ],
            ],
            'double no-empty string object, empty data' => [
                'objectDefinition' => new DoubleNonEmptyStringObjectDefinition('key1', 'key2'),
                'data' => [],
            ],
            'double non-empty string object, key1 not present' => [
                'objectDefinition' => new DoubleNonEmptyStringObjectDefinition('key1', 'key2'),
                'data' => [
                    'key2' => 'non-relevant value',
                ],
            ],
            'double non-empty string object, key2 not present' => [
                'objectDefinition' => new DoubleNonEmptyStringObjectDefinition('key1', 'key2'),
                'data' => [
                    'key1' => 'non-relevant value',
                ],
            ],
            'double non-empty string object, type1 invalid' => [
                'objectDefinition' => new DoubleNonEmptyStringObjectDefinition('key1', 'key2'),
                'data' => [
                    'key1' => 100,
                    'key2' => 'non-relevant value',
                ],
            ],
            'double non-empty string object, type2 invalid' => [
                'objectDefinition' => new DoubleNonEmptyStringObjectDefinition('key1', 'key2'),
                'data' => [
                    'key1' => 'non-relevant value',
                    'key2' => '',
                ],
            ],
        ];
    }

    /**
     * @dataProvider createSuccessDataProvider
     *
     * @param array<mixed> $data
     */
    public function testCreateSuccess(
        ObjectDefinitionInterface $objectDefinition,
        array $data,
        object $expected
    ): void {
        self::assertEquals($expected, $this->objectFactory->create($objectDefinition, $data));
    }

    /**
     * @return array<mixed>
     */
    public function createSuccessDataProvider(): array
    {
        return [
            'single string object, empty value' => [
                'objectDefinition' => new SingleStringObjectDefinition('key'),
                'data' => [
                    'key' => '',
                ],
                'expected' => new SingleStringObject(''),
            ],
            'single string object, non-empty value' => [
                'objectDefinition' => new SingleStringObjectDefinition('key'),
                'data' => [
                    'key' => 'non-empty',
                ],
                'expected' => new SingleStringObject('non-empty'),
            ],
            'double non-empty string object' => [
                'objectDefinition' => new DoubleNonEmptyStringObjectDefinition('key1', 'key2'),
                'data' => [
                    'key1' => 'non-empty value 1',
                    'key2' => 'non-empty value 2',
                ],
                'expected' => new DoubleNonEmptyStringObject('non-empty value 1', 'non-empty value 2'),
            ],
        ];
    }
}
