<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use SmartAssert\ServiceClient\Authentication\Authentication;
use SmartAssert\ServiceClient\Exception\CurlExceptionInterface;
use SmartAssert\ServiceClient\ExceptionFactory\CurlExceptionFactory;
use SmartAssert\ServiceClient\Payload\Payload;
use SmartAssert\ServiceClient\Response\ResponseInterface;
use SmartAssert\ServiceClient\ResponseFactory\ResponseFactory;

class Client
{
    public function __construct(
        private readonly RequestFactoryInterface $requestFactory,
        private readonly StreamFactoryInterface $streamFactory,
        private readonly HttpClientInterface $httpClient,
        private readonly ResponseFactory $responseFactory,
        private readonly CurlExceptionFactory $curlExceptionFactory,
    ) {
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RequestExceptionInterface
     * @throws NetworkExceptionInterface
     * @throws CurlExceptionInterface
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

        try {
            return $this->responseFactory->create(
                $this->httpClient->sendRequest($httpRequest)
            );
        } catch (NetworkExceptionInterface $networkException) {
            $curlException = $this->curlExceptionFactory->createFromNetworkException($networkException);

            if ($curlException instanceof CurlExceptionInterface) {
                throw $curlException;
            }

            throw $networkException;
        }
    }
}
