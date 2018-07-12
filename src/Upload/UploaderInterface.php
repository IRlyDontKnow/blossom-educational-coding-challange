<?php

declare(strict_types=1);

namespace Blossom\BackendDeveloperTest\Upload;

use Blossom\BackendDeveloperTest\Upload\Exceptions\FailedToResolveStorageException;

interface UploaderInterface
{
    /**
     * @throws FailedToResolveStorageException
     */
    function upload(\SplFileInfo $file, string $uploadServiceName): string;
}
