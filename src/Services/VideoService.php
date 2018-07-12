<?php

declare(strict_types=1);

namespace Blossom\BackendDeveloperTest\Services;

use Blossom\BackendDeveloperTest\DTO\VideoUploadResult;
use Blossom\BackendDeveloperTest\Encoding\EncoderResolverInterface;
use Blossom\BackendDeveloperTest\Encoding\MP4\MP4ConverterInterface;
use Blossom\BackendDeveloperTest\Upload\UploaderInterface;

class VideoService implements VideoServiceInterface
{
    /** @var UploaderInterface */
    private $uploader;

    /** @var MP4ConverterInterface */
    private $mp4Converter;

    /** @var EncoderResolverInterface */
    private $encoderResolver;

    public function __construct(
        UploaderInterface $uploader,
        MP4ConverterInterface $mp4Converter,
        EncoderResolverInterface $encoderResolver
    ) {
        $this->uploader = $uploader;
        $this->mp4Converter = $mp4Converter;
        $this->encoderResolver = $encoderResolver;
    }

    public function uploadVideo(\SplFileInfo $file, string $uploadServiceName, array $formats = []): VideoUploadResult
    {
        $originalFileUrl = $this->uploader->upload($file, $uploadServiceName);
        $formatsResult = [];

        foreach ($formats as $format) {
            $formatsResult[$format] = $this->encode($file, $format, $uploadServiceName);
        }

        return new VideoUploadResult($originalFileUrl, $formatsResult);
    }

    /**
     * Encodes video file and returns url to encoded file.
     *
     * @param \SplFileInfo $file
     * @param string $format
     * @param string $uploadServiceName
     * @return string
     * @throws \Blossom\BackendDeveloperTest\Encoding\Exceptions\FailedToResolveEncoder
     * @throws \Blossom\BackendDeveloperTest\Upload\Exceptions\FailedToResolveStorageException
     */
    private function encode(\SplFileInfo $file, string $format, string $uploadServiceName): string
    {
        if (strtolower($format) === 'mp4') {
            $convertedFile = $this->mp4Converter->convert($file);
            return $this->uploader->upload($convertedFile, $uploadServiceName);
        }

        $encoder = $this->encoderResolver->resolve($format);
        return $encoder->encode($file, $format);
    }
}
