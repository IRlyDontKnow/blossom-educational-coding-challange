<?php

declare(strict_types=1);

namespace Blossom\BackendDeveloperTest\Upload\Storage;

interface StorageInterface
{
    function upload(\SplFileInfo $file): string;
}
