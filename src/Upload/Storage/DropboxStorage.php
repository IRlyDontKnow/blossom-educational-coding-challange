<?php

declare(strict_types=1);

namespace Blossom\BackendDeveloperTest\Upload\Storage;

use DropboxStub\DropboxClient;

class DropboxStorage implements StorageInterface
{
    /** @var DropboxClient */
    private $dropboxClient;

    public function __construct(string $accessKey, string $secretToken, string $container)
    {
        $this->dropboxClient = new DropboxClient($accessKey, $secretToken, $container);
    }

    function upload(\SplFileInfo $file): string
    {
        return $this->dropboxClient->upload($file);
    }
}
