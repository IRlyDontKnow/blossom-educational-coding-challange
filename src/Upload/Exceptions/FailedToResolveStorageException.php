<?php

declare(strict_types=1);

namespace Blossom\BackendDeveloperTest\Upload\Exceptions;

class FailedToResolveStorageException extends \Exception
{
    public function __construct(string $storageId)
    {
        parent::__construct(sprintf('Could not resolve storage with id "%s".', $storageId));
    }
}
