<?php

namespace Blossom\BackendDeveloperTest\Tests\Services;

use Blossom\BackendDeveloperTest\DTO\VideoUploadResult;
use Blossom\BackendDeveloperTest\Encoding\EncoderResolverInterface;
use Blossom\BackendDeveloperTest\Encoding\Encoders\EncoderInterface;
use Blossom\BackendDeveloperTest\Encoding\MP4\MP4ConverterInterface;
use Blossom\BackendDeveloperTest\Services\VideoService;
use Blossom\BackendDeveloperTest\Upload\UploaderInterface;
use PHPUnit\Framework\TestCase;

class VideoServiceTest extends TestCase
{
    public function testUploadingVideo()
    {
        $videoFile = new \SplFileInfo('video.mp4');
        $uploader = $this->createMock(UploaderInterface::class);

        $uploader
            ->expects($this->once())
            ->method('upload')
            ->with($videoFile, 'ftp')
            ->willReturn('ftp://files/video.mp4');

        $videoService = new VideoService(
            $uploader,
            $this->createMock(MP4ConverterInterface::class),
            $this->createMock(EncoderResolverInterface::class)
        );

        $result = $videoService->uploadVideo($videoFile, 'ftp', []);

        $this->assertInstanceOf(VideoUploadResult::class, $result);
        $this->assertEquals('ftp://files/video.mp4', $result->url);
        $this->assertEmpty($result->formats);
    }

    public function testUploadingVideoWithMp4Format()
    {
        $videoFile = new \SplFileInfo('video.mp4');
        $convertedFile = new \SplFileInfo('video.encoded.mp4');

        $uploader = $this->createMock(UploaderInterface::class);
        $uploader
            ->expects($this->exactly(2))
            ->method('upload')
            ->withConsecutive([$videoFile, 'ftp'], [$convertedFile, 'ftp'])
            ->willReturnOnConsecutiveCalls('ftp://files/video.mp4', 'ftp://viles/video.encoded.mp4');

        $mp4Converter = $this->createMock(MP4ConverterInterface::class);
        $mp4Converter
            ->expects($this->once())
            ->method('convert')
            ->with($videoFile)
            ->willReturn($convertedFile);

        $videoService = new VideoService(
            $uploader,
            $mp4Converter,
            $this->createMock(EncoderResolverInterface::class)
        );

        $result = $videoService->uploadVideo($videoFile, 'ftp', ['mp4']);

        $this->assertInstanceOf(VideoUploadResult::class, $result);
        $this->assertEquals('ftp://files/video.mp4', $result->url);
        $this->assertEquals('ftp://viles/video.encoded.mp4', $result->formats['mp4']);
    }

    public function testUploadingVideoWithMultipleFormats()
    {
        $videoFile = new \SplFileInfo('video.mp4');

        $uploader = $this->createMock(UploaderInterface::class);
        $uploader
            ->expects($this->once())
            ->method('upload')
            ->with($videoFile, 'ftp')
            ->willReturn('ftp://files/video.mp4');

        $encoderResolver = $this->createMock(EncoderResolverInterface::class);
        $encoderResolver
            ->expects($this->exactly(2))
            ->method('resolve')
            ->withConsecutive(['webm'], ['avi'])
            ->willReturnCallback(function ($format) use ($videoFile) {
                $encoder = $this->createMock(EncoderInterface::class);
                $encoder
                    ->expects($this->once())
                    ->method('encode')
                    ->with($videoFile, $format)
                    ->willReturn('http://encoded.com/video.encoded.' . $format);
                return $encoder;
            });

        $videoService = new VideoService(
            $uploader,
            $this->createMock(MP4ConverterInterface::class),
            $encoderResolver
        );

        $result = $videoService->uploadVideo($videoFile, 'ftp', ['webm', 'avi']);

        $this->assertInstanceOf(VideoUploadResult::class, $result);
        $this->assertEquals('ftp://files/video.mp4', $result->url);
        $this->assertEquals('http://encoded.com/video.encoded.webm', $result->formats['webm']);
        $this->assertEquals('http://encoded.com/video.encoded.avi', $result->formats['avi']);
    }
}
