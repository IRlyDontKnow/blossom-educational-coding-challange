<?php

declare(strict_types=1);

namespace Blossom\BackendDeveloperTest\Upload\Storage;

use S3Stub\Client;

class AmazonS3Storage implements StorageInterface
{
    /** @var string */
    private $bucketName;

    /** @var Client */
    private $s3Client;

    public function __construct(string $accessKeyId, string $secretAccessKey, string $bucketName)
    {
        $this->bucketName = $bucketName;
        $this->s3Client = new Client($accessKeyId, $secretAccessKey);
    }

    function upload(\SplFileInfo $file): string
    {
        $fileObject = $this->s3Client->send($file, $this->bucketName);
        return $fileObject->getPublicUrl();
    }
}
