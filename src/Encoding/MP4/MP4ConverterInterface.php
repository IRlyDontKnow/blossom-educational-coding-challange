<?php

declare(strict_types=1);

namespace Blossom\BackendDeveloperTest\Encoding\MP4;

interface MP4ConverterInterface
{
    function convert(\SplFileInfo $sourceFile): \SplFileInfo;
}
