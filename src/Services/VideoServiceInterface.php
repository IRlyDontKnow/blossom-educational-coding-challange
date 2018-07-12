<?php

declare(strict_types=1);

namespace Blossom\BackendDeveloperTest\Services;

use Blossom\BackendDeveloperTest\DTO\VideoUploadResult;

interface VideoServiceInterface
{
    function uploadVideo(\SplFileInfo $file, string $uploadService, array $formats = []): VideoUploadResult;
}
