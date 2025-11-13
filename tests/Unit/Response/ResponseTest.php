<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Tests\Unit\Response;

use GuzzleHttp\Psr7\Response as HttpResponse;
use PHPUnit\Framework\TestCase;
use SmartAssert\ServiceClient\Response\Response;

class ResponseTest extends TestCase
{
    /**
     * @dataProvider isSuccessfulDataProvider
     */
    public function testIsSuccessful(Response $response, bool $expected): void
    {
        self::assertSame($expected, $response->isSuccessful());
    }

    /**
     * @return array<mixed>
     */
    public static function isSuccessfulDataProvider(): array
    {
        return [
            '100 is successful' => [
                'response' => new Response(new HttpResponse(100)),
                'expected' => true,
            ],
            '200 is successful' => [
                'response' => new Response(new HttpResponse(200)),
                'expected' => true,
            ],
            '300 is not successful' => [
                'response' => new Response(new HttpResponse(300)),
                'expected' => false,
            ],
            '400 is not successful' => [
                'response' => new Response(new HttpResponse(400)),
                'expected' => false,
            ],
            '500 is not successful' => [
                'response' => new Response(new HttpResponse(500)),
                'expected' => false,
            ],
        ];
    }

    public function testGetHttpResponse(): void
    {
        $httpResponse = new HttpResponse();

        $response = new Response($httpResponse);

        self::assertSame($httpResponse, $response->getHttpResponse());
    }

    public function testGetStatusCode(): void
    {
        for ($statusCode = 100; $statusCode <= 599; ++$statusCode) {
            $httpResponse = new HttpResponse($statusCode);
            $response = new Response($httpResponse);

            self::assertSame($statusCode, $response->getStatusCode());
        }
    }
}
