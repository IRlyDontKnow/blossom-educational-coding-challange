<?php

declare(strict_types=1);

namespace Blossom\BackendDeveloperTest\Encoding\Exceptions;

class FailedToResolveEncoder extends \Exception
{
    /** @var string */
    private $format;

    public function __construct(string $format)
    {
        $this->format = $format;
        parent::__construct(sprintf('Could not resolve encoder for format "%s".', $format));
    }

    public function getFormat(): string
    {
        return $this->format;
    }
}
