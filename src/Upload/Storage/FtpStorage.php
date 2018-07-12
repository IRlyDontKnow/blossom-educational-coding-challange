<?php

declare(strict_types=1);

namespace Blossom\BackendDeveloperTest\Upload\Storage;

use FTPStub\FTPUploader;

class FtpStorage implements StorageInterface
{
    /** @var FTPUploader */
    private $ftpUploader;

    /** @var string */
    private $hostname;

    /** @var string */
    private $username;

    /** @var string */
    private $password;

    /** @var string */
    private $destination;

    public function __construct(string $hostname, string $username, string $password, string $destination)
    {
        $this->ftpUploader = new FTPUploader();
        $this->hostname = $hostname;
        $this->username = $username;
        $this->password = $password;
        $this->destination = $destination;
    }

    function upload(\SplFileInfo $file): string
    {
        $this->ftpUploader->uploadFile($file, $this->hostname, $this->username, $this->password, $this->destination);
        return 'ftp://uploads.blossomeducational.com/' . $this->destination . '/' . $file->getFilename();
    }
}
