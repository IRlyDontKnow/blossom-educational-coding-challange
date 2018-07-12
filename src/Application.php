<?php

namespace Blossom\BackendDeveloperTest;

use Blossom\BackendDeveloperTest\Upload\Exceptions\FailedToResolveStorageException;
use Blossom\BackendDeveloperTest\Upload\Storage\AmazonS3Storage;
use Blossom\BackendDeveloperTest\Upload\Storage\DropboxStorage;
use Blossom\BackendDeveloperTest\Upload\Storage\FtpStorage;
use Blossom\BackendDeveloperTest\Upload\Uploader;
use Blossom\BackendDeveloperTest\Upload\UploaderInterface;
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
    /** @var UploaderInterface */
    private $uploader;

    /**
     * By default the constructor takes a single argument which is a config array.
     *
     * You can handle it however you want.
     *
     * @param array $config Application config.
     */
    public function __construct(array $config)
    {
        $this->uploader = new Uploader([
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

        $uploadService = $request->get('upload');
        $file = $request->files->get('file');

        try {
            $uploadedFileUrl = $this->uploader->upload($file, $uploadService);

            return $this->createResponse([
                'url' => $uploadedFileUrl,
            ]);
        } catch (FailedToResolveStorageException $ex) {
            return $this->createErrorResponse('Unknown upload service.');
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
