<?php

namespace Blossom\BackendDeveloperTest\Tests\Upload;

use Blossom\BackendDeveloperTest\Upload\Exceptions\FailedToResolveStorageException;
use Blossom\BackendDeveloperTest\Upload\Storage\StorageInterface;
use Blossom\BackendDeveloperTest\Upload\Uploader;
use PHPUnit\Framework\TestCase;

class UploaderTest extends TestCase
{
    public function testUploading()
    {
        $ftpStorage = $this->createMock(StorageInterface::class);
        $ftpStorage
            ->expects($this->once())
            ->method('upload')
            ->willReturn('ftp://files.com/test.mp4');

        $uploader = new Uploader([
            'ftp' => $ftpStorage,
        ]);
        $url = $uploader->upload(new \SplFileInfo('test.mp4'), 'ftp');

        $this->assertEquals('ftp://files.com/test.mp4', $url);
    }

    public function testThrowsFailedToResolveStorageExceptionForInvalidUploadServiceName()
    {
        $uploader = new Uploader([]);
        $this->expectException(FailedToResolveStorageException::class);
        $uploader->upload(new \SplFileInfo('test.mp4'), 's3');
    }
}
