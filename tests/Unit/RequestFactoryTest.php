<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use SmartAssert\SecurityTokenExtractor\TokenExtractor;
use SmartAssert\ServiceClient\Authentication\BearerAuthentication;
use SmartAssert\ServiceClient\Request;
use SmartAssert\ServiceClient\RequestFactory;

class RequestFactoryTest extends TestCase
{
    private RequestFactory $requestFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->requestFactory = new RequestFactory(
            new TokenExtractor()
        );
    }

    /**
     * @dataProvider createWithAuthenticationFromHttpRequestDataProvider
     */
    public function testCreateWithAuthenticationFromHttpRequest(
        string $method,
        string $url,
        RequestInterface $httpRequest,
        Request $expected
    ): void {
        self::assertEquals(
            $expected,
            $this->requestFactory->createWithAuthenticationFromHttpRequest($method, $url, $httpRequest)
        );
    }

    /**
     * @return array<mixed>
     */
    public function createWithAuthenticationFromHttpRequestDataProvider(): array
    {
        return [
            'no request headers' => [
                'method' => 'GET',
                'url' => 'http://example.com/no-request-headers',
                'httpRequest' => (function () {
                    $request = \Mockery::mock(RequestInterface::class);
                    $request
                        ->shouldReceive('getHeaderLine')
                        ->with('Authorization')
                        ->andReturn('')
                    ;

                    return $request;
                })(),
                'expected' => new Request('GET', 'http://example.com/no-request-headers')
            ],
            'non-matching authorization header' => [
                'method' => 'GET',
                'url' => 'http://example.com/non-matching-authorization-header',
                'httpRequest' => (function () {
                    $request = \Mockery::mock(RequestInterface::class);
                    $request
                        ->shouldReceive('getHeaderLine')
                        ->with('Authorization')
                        ->andReturn('non-matching content')
                    ;

                    return $request;
                })(),
                'expected' => new Request('GET', 'http://example.com/non-matching-authorization-header')
            ],
            'matching authorization header' => [
                'method' => 'GET',
                'url' => 'http://example.com/matching-authorization-header',
                'httpRequest' => (function () {
                    $request = \Mockery::mock(RequestInterface::class);
                    $request
                        ->shouldReceive('getHeaderLine')
                        ->with('Authorization')
                        ->andReturn('Bearer token-value')
                    ;

                    return $request;
                })(),
                'expected' => (new Request('GET', 'http://example.com/matching-authorization-header'))
                    ->withAuthentication(new BearerAuthentication('token-value'))
            ],
        ];
    }
}
