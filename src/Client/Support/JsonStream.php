<?php

namespace Xingo\IDServer\Client\Support;

use GuzzleHttp\Psr7\StreamDecoratorTrait;
use JsonSerializable;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

class JsonStream implements StreamInterface, JsonSerializable
{
    use StreamDecoratorTrait;

    /**
     * Return json representation of the stream.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $content = (string)$this->getContents();
        if ($content === '') {
            return null;
        }

        $decodedContent = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException(
                'Error trying to decode response: ' .
                json_last_error_msg()
            );
        }
        return $decodedContent;
    }

    /**
     * Return json representation of the stream.
     *
     * @return array
     */
    public function asJson()
    {
        return $this->jsonSerialize();
    }
}