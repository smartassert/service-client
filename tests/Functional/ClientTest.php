<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Tests\Functional;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\Psr7\Response as HttpResponse;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;
use SmartAssert\ServiceClient\Authentication\Authentication;
use SmartAssert\ServiceClient\Authentication\BearerAuthentication;
use SmartAssert\ServiceClient\Client;
use SmartAssert\ServiceClient\ExceptionFactory\CurlExceptionFactory;
use SmartAssert\ServiceClient\Payload\JsonPayload;
use SmartAssert\ServiceClient\Payload\Payload;
use SmartAssert\ServiceClient\Request;
use SmartAssert\ServiceClient\Response\JsonResponse;
use SmartAssert\ServiceClient\Response\Response;
use SmartAssert\ServiceClient\Response\ResponseInterface;
use SmartAssert\ServiceClient\ResponseFactory\ResponseFactory;
use SmartAssert\ServiceClient\Tests\SerializablePayload;
use webignition\HttpHistoryContainer\Container as HttpHistoryContainer;

class ClientTest extends TestCase
{
    protected MockHandler $mockHandler;
    protected Client $client;
    private HttpHistoryContainer $httpHistoryContainer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);

        $this->httpHistoryContainer = new HttpHistoryContainer();
        $handlerStack->push(Middleware::history($this->httpHistoryContainer));

        $httpFactory = new HttpFactory();
        $this->client = new Client(
            $httpFactory,
            $httpFactory,
            new HttpClient(['handler' => $handlerStack]),
            ResponseFactory::createFactory(),
            new CurlExceptionFactory(),
        );
    }

    /**
     * @dataProvider sendRequestCreatesCorrectHttpRequestDataProvider
     */
    public function testSendRequestCreatesCorrectHttpRequest(Request $request, RequestInterface $expected): void
    {
        $this->mockHandler->append(new HttpResponse());

        $this->client->sendRequest($request);

        $lastRequest = $this->getLastRequest();

        self::assertSame($expected->getMethod(), $lastRequest->getMethod());
        self::assertSame((string) $expected->getUri(), (string) $lastRequest->getUri());

        $sentHeaders = $lastRequest->getHeaders();
        unset($sentHeaders['User-Agent']);

        self::assertEquals($expected->getHeaders(), $sentHeaders);
        self::assertSame($expected->getBody()->getContents(), $lastRequest->getBody()->getContents());
    }

    /**
     * @return array<mixed>
     */
    public function sendRequestCreatesCorrectHttpRequestDataProvider(): array
    {
        $textPlainPayload = 'text plain payload';
        $jsonPayloadData = ['key1' => 'value1', 'key2' => 'value2'];
        $jsonPayload = (string) json_encode($jsonPayloadData);

        return [
            'GET with no authentication, no payload' => [
                'request' => new Request('GET', 'http://example.com/get'),
                'expected' => new GuzzleRequest('GET', 'http://example.com/get'),
            ],
            'POST with no authentication, no payload' => [
                'request' => new Request('POST', 'http://example.com/post'),
                'expected' => new GuzzleRequest('POST', 'http://example.com/post'),
            ],
            'POST with authentication, no payload' => [
                'request' => (new Request('POST', 'http://example.com/post'))
                    ->withAuthentication(new Authentication('authentication value')),
                'expected' => new GuzzleRequest(
                    'POST',
                    'http://example.com/post',
                    [
                        'Authorization' => 'authentication value',
                    ]
                ),
            ],
            'POST with bearer authentication, no payload' => [
                'request' => (new Request('POST', 'http://example.com/post'))
                    ->withAuthentication(new BearerAuthentication('authentication value')),
                'expected' => new GuzzleRequest(
                    'POST',
                    'http://example.com/post',
                    [
                        'Authorization' => 'Bearer authentication value',
                    ]
                ),
            ],
            'POST with no authentication, generic payload' => [
                'request' => (new Request('POST', 'http://example.com/post'))
                    ->withPayload(new Payload('text/plain', $textPlainPayload)),
                'expected' => new GuzzleRequest(
                    'POST',
                    'http://example.com/post',
                    [
                        'Content-Type' => 'text/plain',
                        'Content-Length' => (string) strlen($textPlainPayload)
                    ],
                    $textPlainPayload
                ),
            ],
            'POST with no authentication, json payload with array' => [
                'request' => (new Request('POST', 'http://example.com/post'))
                    ->withPayload(new JsonPayload($jsonPayloadData)),
                'expected' => new GuzzleRequest(
                    'POST',
                    'http://example.com/post',
                    [
                        'Content-Type' => 'application/json',
                        'Content-Length' => (string) strlen($jsonPayload)
                    ],
                    $jsonPayload
                ),
            ],
            'POST with no authentication, json payload with JsonSerializable' => [
                'request' => (new Request('POST', 'http://example.com/post'))
                    ->withPayload(new JsonPayload(new SerializablePayload($jsonPayloadData))),
                'expected' => new GuzzleRequest(
                    'POST',
                    'http://example.com/post',
                    [
                        'Content-Type' => 'application/json',
                        'Content-Length' => (string) strlen($jsonPayload)
                    ],
                    $jsonPayload
                ),
            ],
        ];
    }

    /**
     * @dataProvider sendRequestCreatesCorrectResponseDataProvider
     */
    public function testSendRequestCreatesCorrectResponse(
        HttpResponseInterface $httpResponse,
        ResponseInterface $expected,
    ): void {
        $this->mockHandler->append($httpResponse);

        self::assertEquals(
            $expected,
            $this->client->sendRequest(new Request('GET', 'http://example.com/' . md5((string) rand())))
        );
    }

    /**
     * @return array<mixed>
     */
    public function sendRequestCreatesCorrectResponseDataProvider(): array
    {
        $responseTextPlainNoBody = new HttpResponse(200, ['content-type' => 'text/plain']);
        $responseApplicationJsonNoBody = new HttpResponse(200, ['content-type' => 'application/json']);
        $responseTextPlain = new HttpResponse(200, ['content-type' => 'text/plain'], 'text plain content');
        $responseApplicationJson = new HttpResponse(
            200,
            ['content-type' => 'application/json'],
            (string) json_encode(['key' => 'value'])
        );

        return [
            'text/plain, no body' => [
                'httpResponse' => $responseTextPlainNoBody,
                'expected' => new Response($responseTextPlainNoBody),
            ],
            'application/json, no body' => [
                'httpResponse' => $responseApplicationJsonNoBody,
                'expected' => new JsonResponse($responseApplicationJsonNoBody),
            ],
            'text/plain' => [
                'httpResponse' => $responseTextPlain,
                'expected' => new Response($responseTextPlain),
            ],
            'application/json' => [
                'httpResponse' => $responseApplicationJson,
                'expected' => new JsonResponse($responseApplicationJson),
            ],
        ];
    }

    private function getLastRequest(): RequestInterface
    {
        $request = $this->httpHistoryContainer->getTransactions()->getRequests()->getLast();
        \assert($request instanceof RequestInterface);

        return $request;
    }
}
