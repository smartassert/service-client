<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\ResponseFactory;

use Psr\Http\Message\ResponseInterface as HttpResponseInterface;
use SmartAssert\ServiceClient\Response\Response;
use SmartAssert\ServiceClient\Response\ResponseInterface;

class ResponseFactory
{
    /**
     * @param ResponseHandlerInterface[] $responseHandlers
     */
    public function __construct(
        private readonly array $responseHandlers = [],
    ) {
    }

    public static function createFactory(): ResponseFactory
    {
        return new ResponseFactory([
            new JsonResponseHandler(),
        ]);
    }

    public function create(HttpResponseInterface $httpResponse): ResponseInterface
    {
        $contentType = $httpResponse->getHeaderLine('content-type');

        foreach ($this->responseHandlers as $responseHandler) {
            if ($responseHandler->handles($contentType)) {
                return $responseHandler->create($httpResponse);
            }
        }

        return new Response($httpResponse);
    }
}
