<?php

declare(strict_types=1);

namespace Blossom\BackendDeveloperTest\Encoding\Encoders;

use EncodingStub\Client;

class EncodingComEncoder implements EncoderInterface
{
    /** @var Client */
    private $client;

    public function __construct(string $appId, string $accessToken)
    {
        $this->client = new Client($appId, $accessToken);
    }

    function encode(\SplFileInfo $file, string $format): string
    {
        return $this->client->encodeFile($file, $format);
    }
}
