<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use SmartAssert\ServiceClient\Authentication\Authentication;
use SmartAssert\ServiceClient\Exception\InvalidResponseContentException;
use SmartAssert\ServiceClient\Exception\InvalidResponseDataException;
use SmartAssert\ServiceClient\Payload\Payload;
use SmartAssert\ServiceClient\Response\Response;
use SmartAssert\ServiceClient\Response\ResponseInterface;

class Client
{
    public function __construct(
        private readonly RequestFactoryInterface $requestFactory,
        private readonly StreamFactoryInterface $streamFactory,
        private readonly HttpClientInterface $httpClient,
        private readonly ResponseDecoder $responseDecoder,
    ) {
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function sendRequest(Request $request): ResponseInterface
    {
        $httpRequest = $this->requestFactory->createRequest(
            $request->method,
            $request->url
        );

        $authentication = $request->getAuthentication();
        if ($authentication instanceof Authentication) {
            $httpRequest = $httpRequest->withHeader('Authorization', $authentication->value);
        }

        $payload = $request->getPayload();
        if ($payload instanceof Payload) {
            $httpRequest = $httpRequest
                ->withHeader('Content-Type', $payload->contentType)
                ->withBody($this->streamFactory->createStream($payload->data))
            ;
        }

        return new Response($this->httpClient->sendRequest($httpRequest));
    }

    /**
     * @return array<mixed>
     *
     * @throws ClientExceptionInterface
     * @throws InvalidResponseContentException
     * @throws InvalidResponseDataException
     */
    public function sendRequestForJsonEncodedData(Request $request): array
    {
        $response = $this->sendRequest($request);

        return $this->responseDecoder->decodedJsonResponse($response->getHttpResponse());
    }
}
