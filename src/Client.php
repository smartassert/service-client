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
use SmartAssert\ServiceClient\Exception\InvalidResponseTypeException;
use SmartAssert\ServiceClient\Exception\NonSuccessResponseException;
use SmartAssert\ServiceClient\Exception\UnauthorizedException;
use SmartAssert\ServiceClient\ExceptionFactory\CurlExceptionFactory;
use SmartAssert\ServiceClient\Payload\Payload;
use SmartAssert\ServiceClient\Response\JsonResponse;
use SmartAssert\ServiceClient\Response\ResponseInterface;
use SmartAssert\ServiceClient\ResponseFactory\ResponseFactory;

readonly class Client
{
    public function __construct(
        private RequestFactoryInterface $requestFactory,
        private StreamFactoryInterface $streamFactory,
        private HttpClientInterface $httpClient,
        private ResponseFactory $responseFactory,
        private CurlExceptionFactory $curlExceptionFactory,
    ) {
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RequestExceptionInterface
     * @throws NetworkExceptionInterface
     * @throws CurlExceptionInterface
     * @throws UnauthorizedException
     * @throws NonSuccessResponseException
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
            $response = $this->responseFactory->create(
                $this->httpClient->sendRequest($httpRequest)
            );

            if (401 === $response->getStatusCode()) {
                throw new UnauthorizedException();
            }

            if (!$response->isSuccessful()) {
                throw new NonSuccessResponseException($response);
            }

            return $response;
        } catch (NetworkExceptionInterface $networkException) {
            $curlException = $this->curlExceptionFactory->createFromNetworkException($networkException);

            if ($curlException instanceof CurlExceptionInterface) {
                throw $curlException;
            }

            throw $networkException;
        }
    }

    /**
     * @throws ClientExceptionInterface
     * @throws CurlExceptionInterface
     * @throws InvalidResponseTypeException
     * @throws NetworkExceptionInterface
     * @throws RequestExceptionInterface
     * @throws UnauthorizedException
     * @throws NonSuccessResponseException
     */
    public function sendRequestForJson(Request $request): JsonResponse
    {
        $response = $this->sendRequest($request);

        if (!$response instanceof JsonResponse) {
            throw InvalidResponseTypeException::create($response, JsonResponse::class);
        }

        return $response;
    }
}
