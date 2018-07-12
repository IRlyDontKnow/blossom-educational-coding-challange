<?php

declare(strict_types=1);

namespace Blossom\BackendDeveloperTest\DTO;

class VideoUploadResult
{
    /** @var string|null */
    public $url;

    /** @var array */
    public $formats;

    public function __construct(string $url, array $formats = [])
    {
        $this->url = $url;
        $this->formats = $formats;
    }
}
