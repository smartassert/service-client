<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Tests\Unit\RequestFactory;

use PHPUnit\Framework\TestCase;
use SmartAssert\ServiceClient\Authentication\Authentication;
use SmartAssert\ServiceClient\Authentication\BearerAuthentication;
use SmartAssert\ServiceClient\Request;
use SmartAssert\ServiceClient\RequestFactory\AuthenticationMiddleware;
use SmartAssert\ServiceClient\RequestFactory\RequestFactory;
use SmartAssert\ServiceClient\RequestFactory\RequestMiddlewareCollection;

class RequestFactoryTest extends TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param non-empty-string $method
     */
    public function testCreate(
        RequestFactory $requestFactory,
        string $method,
        string $url,
        Request $expected
    ): void {
        self::assertEquals($expected, $requestFactory->create($method, $url));
    }

    /**
     * @return array<mixed>
     */
    public function createDataProvider(): array
    {
        $method = md5((string) rand());
        $url = 'https://example.com/' . md5((string) rand());

        $genericAuthentication = new Authentication('generic authentication');
        $bearerAuthentication = new BearerAuthentication('bearer authentication');

        return [
            'default' => [
                'requestFactory' => new RequestFactory(),
                'method' => $method,
                'url' => $url,
                'expected' => new Request($method, $url),
            ],
            'with generic authentication middleware' => [
                'requestFactory' => new RequestFactory(
                    (new RequestMiddlewareCollection())
                        ->set(
                            'authentication',
                            (new AuthenticationMiddleware())->setAuthentication($genericAuthentication)
                        )
                ),
                'method' => $method,
                'url' => $url,
                'expected' => (new Request($method, $url))->withAuthentication($genericAuthentication),
            ],
            'with bearer authentication middleware' => [
                'requestFactory' => new RequestFactory(
                    (new RequestMiddlewareCollection())
                        ->set(
                            'authentication',
                            (new AuthenticationMiddleware())->setAuthentication($bearerAuthentication)
                        )
                ),
                'method' => $method,
                'url' => $url,
                'expected' => (new Request($method, $url))->withAuthentication($bearerAuthentication),
            ],
        ];
    }
}
