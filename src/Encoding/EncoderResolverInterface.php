<?php

declare(strict_types=1);

namespace Blossom\BackendDeveloperTest\Encoding;

use Blossom\BackendDeveloperTest\Encoding\Encoders\EncoderInterface;
use Blossom\BackendDeveloperTest\Encoding\Exceptions\FailedToResolveEncoder;

interface EncoderResolverInterface
{
    /**
     * @throws FailedToResolveEncoder
     */
    function resolve(string $format): EncoderInterface;
}
