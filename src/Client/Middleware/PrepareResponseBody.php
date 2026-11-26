<?php

namespace Xingo\IDServer\Client\Middleware;

use GuzzleHttp\Psr7\CachingStream;
use Psr\Http\Message\ResponseInterface;
use Xingo\IDServer\Client\Support\JsonStream;

class PrepareResponseBody
{
    public function __invoke(ResponseInterface $response): ResponseInterface
    {
        $body = $response->getBody();
        $contentType = strtolower($response->getHeaderLine('Content-Type'));
        $isJson = strpos($contentType, 'json') !== false;

        // Downloads must remain unread until the export callback relays them.
        if ($response->getStatusCode() < 400 && !$isJson &&
            (!$body->isSeekable() || strpos($contentType, 'text/csv') !== false ||
                strpos($contentType, 'application/octet-stream') !== false)) {
            return $response;
        }

        // Token refresh and exception handling both read and rewind JSON errors.
        if (!$body->isSeekable()) {
            $body = new CachingStream($body);
        }

        return $response->withBody(new JsonStream($body));
    }
}
