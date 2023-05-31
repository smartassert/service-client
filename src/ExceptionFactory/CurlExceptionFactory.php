<?php

declare(strict_types=1);

namespace SmartAssert\ServiceClient\ExceptionFactory;

use Psr\Http\Client\NetworkExceptionInterface;
use SmartAssert\ServiceClient\Exception\CurlException;
use SmartAssert\ServiceClient\Exception\CurlExceptionInterface;

class CurlExceptionFactory
{
    private const CURL_CODE_PREFIX = 'cURL error ';

    public function createFromNetworkException(NetworkExceptionInterface $exception): ?CurlExceptionInterface
    {
        $exceptionMessage = $exception->getMessage();
        if (!str_starts_with($exceptionMessage, self::CURL_CODE_PREFIX)) {
            return null;
        }

        $exceptionMessage = substr($exceptionMessage, strlen(self::CURL_CODE_PREFIX));

        $curlCodeMatches = [];
        preg_match('/^[0-9]{1,3}/', $exceptionMessage, $curlCodeMatches);
        if (1 !== count($curlCodeMatches)) {
            return null;
        }

        $code = (int) $curlCodeMatches[0];

        $message = substr($exceptionMessage, strlen((string) $code));
        $message = ltrim($message, ': ');

        return new CurlException($exception->getRequest(), $code, $message, $exception);
    }
}
