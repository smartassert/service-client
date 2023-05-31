<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Tests\Unit\ExceptionFactory;

use GuzzleHttp\Exception\ConnectException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestInterface;
use SmartAssert\ServiceClient\Exception\CurlException;
use SmartAssert\ServiceClient\Exception\CurlExceptionInterface;
use SmartAssert\ServiceClient\ExceptionFactory\CurlExceptionFactory;

class CurlExceptionFactoryTest extends TestCase
{
    /**
     * @dataProvider createFromNetworkExceptionDataProvider
     */
    public function testCreateFromNetworkException(
        NetworkExceptionInterface $networkException,
        ?CurlExceptionInterface $expected
    ): void {
        self::assertEquals(
            $expected,
            (new CurlExceptionFactory())->createFromNetworkException($networkException),
        );
    }

    /**
     * @return array<mixed>
     */
    public function createFromNetworkExceptionDataProvider(): array
    {
        $connectExceptionCurlCode6EmptyMessage = new ConnectException(
            'cURL error 6',
            \Mockery::mock(RequestInterface::class),
        );

        $connectExceptionCurlCode6NonEmptyMessage = new ConnectException(
            'cURL error 9: error 9 message',
            \Mockery::mock(RequestInterface::class),
        );

        $connectExceptionCurlCode54NonEmptyMessage = new ConnectException(
            'cURL error 54: error 54 message',
            \Mockery::mock(RequestInterface::class),
        );

        $connectExceptionCurlCode128NonEmptyMessage = new ConnectException(
            'cURL error 128: error 128 message',
            \Mockery::mock(RequestInterface::class),
        );

        $connectExceptionCurlErrorPrefixWithinMessage = new ConnectException(
            'cURL error 128: cURL error 128: error 128 message',
            \Mockery::mock(RequestInterface::class),
        );

        return [
            'code part does not start with "cURL error "' => [
                'networkException' => new ConnectException(
                    md5((string) rand()),
                    \Mockery::mock(RequestInterface::class),
                ),
                'expected' => null,
            ],
            'code part does not end with integer value' => [
                'networkException' => new ConnectException(
                    'cURL error foo: invalid curl code',
                    \Mockery::mock(RequestInterface::class),
                ),
                'expected' => null,
            ],
            'single digit curl code, empty message' => [
                'networkException' => $connectExceptionCurlCode6EmptyMessage,
                'expected' => new CurlException(
                    $connectExceptionCurlCode6EmptyMessage->getRequest(),
                    6,
                    '',
                    $connectExceptionCurlCode6EmptyMessage
                ),
            ],
            'single digit curl code, non-empty message' => [
                'networkException' => $connectExceptionCurlCode6NonEmptyMessage,
                'expected' => new CurlException(
                    $connectExceptionCurlCode6NonEmptyMessage->getRequest(),
                    9,
                    'error 9 message',
                    $connectExceptionCurlCode6NonEmptyMessage
                ),
            ],
            'double digit curl code, non-empty message' => [
                'networkException' => $connectExceptionCurlCode54NonEmptyMessage,
                'expected' => new CurlException(
                    $connectExceptionCurlCode54NonEmptyMessage->getRequest(),
                    54,
                    'error 54 message',
                    $connectExceptionCurlCode54NonEmptyMessage
                ),
            ],
            'triple digit curl code, non-empty message' => [
                'networkException' => $connectExceptionCurlCode128NonEmptyMessage,
                'expected' => new CurlException(
                    $connectExceptionCurlCode128NonEmptyMessage->getRequest(),
                    128,
                    'error 128 message',
                    $connectExceptionCurlCode128NonEmptyMessage
                ),
            ],
            'curl error code prefix present in message' => [
                'networkException' => $connectExceptionCurlErrorPrefixWithinMessage,
                'expected' => new CurlException(
                    $connectExceptionCurlErrorPrefixWithinMessage->getRequest(),
                    128,
                    'cURL error 128: error 128 message',
                    $connectExceptionCurlErrorPrefixWithinMessage
                ),
            ],
        ];
    }
}
