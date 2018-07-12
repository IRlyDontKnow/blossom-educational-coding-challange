<?php

namespace Blossom\BackendDeveloperTest;

use Blossom\BackendDeveloperTest\Encoding\EncoderResolver;
use Blossom\BackendDeveloperTest\Encoding\Encoders\EncodingComEncoder;
use Blossom\BackendDeveloperTest\Encoding\Exceptions\FailedToResolveEncoder;
use Blossom\BackendDeveloperTest\Encoding\MP4\MP4Converter;
use Blossom\BackendDeveloperTest\Services\VideoService;
use Blossom\BackendDeveloperTest\Upload\Exceptions\FailedToResolveStorageException;
use Blossom\BackendDeveloperTest\Upload\Storage\AmazonS3Storage;
use Blossom\BackendDeveloperTest\Upload\Storage\DropboxStorage;
use Blossom\BackendDeveloperTest\Upload\Storage\FtpStorage;
use Blossom\BackendDeveloperTest\Upload\Uploader;
use FFMPEGStub\FFMPEG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * You should implement this class however you want.
 *
 * The only requirement is existence of public function `handleRequest()`
 * as this is what is tested. The constructor's signature must not be changed.
 */
class Application
{
    /** @var VideoService */
    private $videoService;

    /**
     * By default the constructor takes a single argument which is a config array.
     *
     * You can handle it however you want.
     *
     * @param array $config Application config.
     */
    public function __construct(array $config)
    {
        $uploader = new Uploader([
            'ftp' => new FtpStorage(
                $config['ftp']['hostname'],
                $config['ftp']['username'],
                $config['ftp']['password'],
                $config['ftp']['destination']
            ),
            's3' => new AmazonS3Storage(
                $config['s3']['access_key_id'],
                $config['s3']['secret_access_key'],
                $config['s3']['bucketname']
            ),
            'dropbox' => new DropboxStorage(
                $config['dropbox']['access_key'],
                $config['dropbox']['secret_token'],
                $config['dropbox']['container']
            ),
        ]);
        $mp4Converter = new MP4Converter(new FFMPEG());
        $encodingComEncoder = new EncodingComEncoder(
            $config['encoding.com']['app_id'],
            $config['encoding.com']['access_token']
        );
        $encoderResolver = new EncoderResolver([
            'webm' => $encodingComEncoder,
            'avi' => $encodingComEncoder,
            'ogv' => $encodingComEncoder,
            'mov' => $encodingComEncoder,
        ]);

        $this->videoService = new VideoService($uploader, $mp4Converter, $encoderResolver);
    }

    /**
     * This method should handle a Request that comes pre-filled with various data.
     *
     * You should implement it however you want and it should return a Response
     * that passes all tests found in EncoderTest.
     *
     * @param  Request $request The request.
     *
     * @return Response
     */
    public function handleRequest(Request $request): Response
    {
        if ($request->getMethod() === 'GET') {
            return new Response(null, Response::HTTP_METHOD_NOT_ALLOWED);
        }

        $errorMessage = $this->validateRequest($request);

        if (!empty($errorMessage)) {
            return $this->createErrorResponse($errorMessage);
        }

        try {
            $result = $this->videoService->uploadVideo(
                $request->files->get('file'),
                $request->get('upload'),
                $request->get('formats', [])
            );

            return $this->createResponse([
                'url' => $result->url,
                'formats' => $result->formats,
            ]);
        } catch (FailedToResolveStorageException $ex) {
            return $this->createErrorResponse('Unknown upload service.');
        } catch (FailedToResolveEncoder $ex) {
            return $this->createErrorResponse(sprintf('Format "%s" is not supported.', $ex->getFormat()));
        }
    }

    private function validateRequest(Request $request)
    {
        if ($request->request === null) {
            return 'You did not provide required parameter';
        }

        if (empty($request->get('upload'))) {
            return 'Upload service name cannot be empty.';
        }

        if ($request->files === null || !$request->files->has('file')) {
            return 'You did not provide any file.';
        }

        return null;
    }

    /**
     * Creates json response with preset charset to UTF-8.
     *
     * @param array $data
     * @param int $status
     * @return Response
     */
    private function createResponse(array $data, int $status = Response::HTTP_OK): Response
    {
        $response = new JsonResponse($data, $status);
        $response->setCharset('UTF-8');

        return $response;
    }

    /**
     * Creates json response with a error message.
     *
     * @param string $errorMessage
     * @param int $status
     * @return Response
     */
    private function createErrorResponse(string $errorMessage, int $status = Response::HTTP_BAD_REQUEST): Response
    {
        return $this->createResponse(['error' => $errorMessage], $status);
    }
}
