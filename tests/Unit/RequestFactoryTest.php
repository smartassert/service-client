<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Tests\Unit;

use PHPUnit\Framework\TestCase;
use SmartAssert\ServiceClient\Request;
use SmartAssert\ServiceClient\RequestFactory;

class RequestFactoryTest extends TestCase
{
    private RequestFactory $requestFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->requestFactory = new RequestFactory();
    }

    /**
     * @dataProvider createDataProvider
     *
     * @param non-empty-string $method
     */
    public function testCreate(string $method, string $url, Request $expected): void
    {
        self::assertEquals($expected, $this->requestFactory->create($method, $url));
    }

    /**
     * @return array<mixed>
     */
    public function createDataProvider(): array
    {
        $method = md5((string) rand());
        $url = 'https://example.com/' . md5((string) rand());

        return [
            'default' => [
                'method' => $method,
                'url' => $url,
                'expected' => new Request($method, $url),
            ],
        ];
    }
}
