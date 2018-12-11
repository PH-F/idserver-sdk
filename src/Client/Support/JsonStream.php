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
            return [];
        }

        $decodedContent = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->printDumpAndDieOutput($content);

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

    /**
     * Print the output to the screen when app is running locally and
     * the output seems to be a dd response.
     *
     * @param string $content
     */
    protected function printDumpAndDieOutput($content)
    {
        if (app()->isLocal() && preg_match('/"sf-dump-[0-9]+"/', $content)) {
            echo $content;
            exit;
        }
    }
}
