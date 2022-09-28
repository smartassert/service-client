<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\Tests\Functional;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use SmartAssert\ServiceClient\Authentication\Authentication;
use SmartAssert\ServiceClient\Authentication\BearerAuthentication;
use SmartAssert\ServiceClient\Client;
use SmartAssert\ServiceClient\Exception\InvalidResponseContentException;
use SmartAssert\ServiceClient\Exception\InvalidResponseDataException;
use SmartAssert\ServiceClient\Exception\NonSuccessResponseException;
use SmartAssert\ServiceClient\Payload\JsonPayload;
use SmartAssert\ServiceClient\Payload\Payload;
use SmartAssert\ServiceClient\Request;
use SmartAssert\ServiceClient\ResponseDecoder;
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
            new ResponseDecoder(),
        );
    }

    public function testSendRequestNonSuccessfulResponse(): void
    {
        for ($i = 300; $i <= 599; ++$i) {
            $responseStatusCode = $i;
            $response = new Response($responseStatusCode);

            $this->mockHandler->append($response);

            try {
                $this->client->sendRequest(new Request('GET', 'http://example.com'));
                self::fail(NonSuccessResponseException::class . ' not thrown');
            } catch (NonSuccessResponseException $nonSuccessResponseException) {
                self::assertSame($responseStatusCode, $nonSuccessResponseException->getCode());
            }
        }
    }

    /**
     * @dataProvider sendRequestSuccessDataProvider
     */
    public function testSendRequestSuccess(Request $request, RequestInterface $expectedSentRequest): void
    {
        $this->mockHandler->append(new Response());

        $this->client->sendRequest($request);

        $lastRequest = $this->getLastRequest();

        self::assertSame($expectedSentRequest->getMethod(), $lastRequest->getMethod());
        self::assertSame((string) $expectedSentRequest->getUri(), (string) $lastRequest->getUri());

        $sentHeaders = $lastRequest->getHeaders();
        unset($sentHeaders['User-Agent']);

        self::assertEquals($expectedSentRequest->getHeaders(), $sentHeaders);
        self::assertSame($expectedSentRequest->getBody()->getContents(), $lastRequest->getBody()->getContents());
    }

    /**
     * @dataProvider sendRequestForJsonEncodedDataFailureDataProvider
     *
     * @param class-string $expectedExceptionClass
     */
    public function testSendRequestForJsonEncodedDataFailure(
        ResponseInterface $httpFixture,
        string $expectedExceptionClass
    ): void {
        $this->mockHandler->append($httpFixture);

        try {
            $this->client->sendRequestForJsonEncodedData(new Request('GET', 'http://example.com'));
            self::fail('Exception not thrown');
        } catch (InvalidResponseContentException | InvalidResponseDataException $e) {
            self::assertInstanceOf($expectedExceptionClass, $e);
        }
    }

    /**
     * @return array<mixed>
     */
    public function sendRequestForJsonEncodedDataFailureDataProvider(): array
    {
        return [
            'invalid response content type' => [
                'response' => new Response(200, ['content-type' => 'text/plain']),
                'expectedExceptionClass' => InvalidResponseContentException::class,
            ],
            'invalid response data' => [
                'response' => new Response(200, ['content-type' => 'application/json'], (string) json_encode(true)),
                'expectedExceptionClass' => InvalidResponseDataException::class,
            ],
        ];
    }

    /**
     * @dataProvider sendRequestSuccessDataProvider
     */
    public function testSendRequestForJsonEncodedDataSuccess(
        Request $request,
        RequestInterface $expectedSentRequest
    ): void {
        $this->mockHandler->append(new Response(
            200,
            [
                'content-type' => 'application/json',
            ],
            (string) json_encode(['key' => 'value'])
        ));

        $this->client->sendRequest($request);

        $sentRequest = $this->getLastRequest();

        self::assertSame($expectedSentRequest->getMethod(), $sentRequest->getMethod());
        self::assertSame((string) $expectedSentRequest->getUri(), (string) $sentRequest->getUri());

        $sentHeaders = $sentRequest->getHeaders();
        unset($sentHeaders['User-Agent']);

        self::assertEquals($expectedSentRequest->getHeaders(), $sentHeaders);
        self::assertSame($expectedSentRequest->getBody()->getContents(), $sentRequest->getBody()->getContents());
    }

    /**
     * @return array<mixed>
     */
    public function sendRequestSuccessDataProvider(): array
    {
        $textPlainPayload = 'text plain payload';
        $jsonPayloadData = ['key1' => 'value1', 'key2' => 'value2'];
        $jsonPayload = (string) json_encode($jsonPayloadData);

        return [
            'GET with no authentication, no payload' => [
                'request' => new Request('GET', 'http://example.com/get'),
                'expectedSentRequest' => new GuzzleRequest('GET', 'http://example.com/get'),
            ],
            'POST with no authentication, no payload' => [
                'request' => new Request('POST', 'http://example.com/post'),
                'expectedSentRequest' => new GuzzleRequest('POST', 'http://example.com/post'),
            ],
            'POST with authentication, no payload' => [
                'request' => (new Request('POST', 'http://example.com/post'))
                    ->withAuthentication(new Authentication('authentication value')),
                'expectedSentRequest' => new GuzzleRequest(
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
                'expectedSentRequest' => new GuzzleRequest(
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
                'expectedSentRequest' => new GuzzleRequest(
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
                'expectedSentRequest' => new GuzzleRequest(
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
                'expectedSentRequest' => new GuzzleRequest(
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

    private function getLastRequest(): RequestInterface
    {
        $request = $this->httpHistoryContainer->getTransactions()->getRequests()->getLast();
        \assert($request instanceof RequestInterface);

        return $request;
    }
}
