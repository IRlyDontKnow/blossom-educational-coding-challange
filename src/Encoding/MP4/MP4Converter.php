<?php

declare(strict_types=1);

namespace Blossom\BackendDeveloperTest\Encoding\MP4;

use FFMPEGStub\FFMPEG;

class MP4Converter implements MP4ConverterInterface
{
    /** @var FFMPEG */
    private $ffmpeg;

    public function __construct(FFMPEG $ffmpeg)
    {
        $this->ffmpeg = $ffmpeg;
    }

    function convert(\SplFileInfo $sourceFile): \SplFileInfo
    {
        return $this->ffmpeg->convert($sourceFile);
    }
}

