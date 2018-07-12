<?php

declare(strict_types=1);

namespace Blossom\BackendDeveloperTest\Upload;

use Blossom\BackendDeveloperTest\Upload\Exceptions\FailedToResolveStorageException;
use Blossom\BackendDeveloperTest\Upload\Storage\StorageInterface;

class Uploader implements UploaderInterface
{
    /** @var array<string,Storage> */
    private $storageMap;

    public function __construct(array $storageMap)
    {
        $this->storageMap = $storageMap;
    }

    public function upload(\SplFileInfo $file, string $storageId): string
    {
        $storage = $this->resolveStorage($storageId);
        return $storage->upload($file);
    }

    private function resolveStorage(string $storageId): StorageInterface
    {
        if (!array_key_exists($storageId, $this->storageMap)) {
            throw new FailedToResolveStorageException($storageId);
        }

        return $this->storageMap[$storageId];
    }
}
