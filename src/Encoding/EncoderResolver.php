<?php

declare(strict_types=1);

namespace Blossom\BackendDeveloperTest\Encoding;

use Blossom\BackendDeveloperTest\Encoding\Encoders\EncoderInterface;
use Blossom\BackendDeveloperTest\Encoding\Exceptions\FailedToResolveEncoder;

class EncoderResolver implements EncoderResolverInterface
{
    /** @var array<string, EncoderInterface> */
    private $encoders;

    public function __construct(array $encoders)
    {
        $this->encoders = $encoders;
    }

    function resolve(string $format): EncoderInterface
    {
        if (!array_key_exists($format, $this->encoders)) {
            throw new FailedToResolveEncoder($format);
        }

        return $this->encoders[$format];
    }
}
