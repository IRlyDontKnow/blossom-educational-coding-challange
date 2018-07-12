<?php

declare(strict_types=1);

namespace Blossom\BackendDeveloperTest\Encoding\Encoders;

interface EncoderInterface
{
    function encode(\SplFileInfo $file, string $format): string;
}
