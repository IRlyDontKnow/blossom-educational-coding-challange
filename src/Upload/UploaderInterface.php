<?php

declare(strict_types=1);

namespace Blossom\BackendDeveloperTest\Upload;

interface UploaderInterface
{
    function upload(\SplFileInfo $file, string $storageId): string;
}
