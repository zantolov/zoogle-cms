<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Client;

use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use GuzzleHttp\Psr7\Response;
use Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Configuration\Configuration;

class GoogleDriveClient
{
    private const FIELDS = 'files(*)';
    private const DOC_MIME_TYPE = 'application/vnd.google-apps.document';
    private const FOLDER_MIME_TYPE = 'application/vnd.google-apps.folder';

    private GoogleDriveAuth $auth;
    private Google_Client $client;
    private Configuration $configuration;

    private array $cache = [];

    public function __construct(GoogleDriveAuth $auth, Configuration $configuration)
    {
        $this->auth = $auth;
        $this->configuration = $configuration;
        $this->initializeClient();
    }

    private function initializeClient(): void
    {
        $this->client = new Google_Client();
        $this->client->setApplicationName("Client_Library_Examples");
        $this->client->setScopes(Google_Service_Drive::DRIVE_READONLY);
        $this->client->setClientId($this->auth->getClientId());
        $this->client->setAuthConfig($this->auth->getAuthConfig());
    }

    private function cached(string $key, callable $callback)
    {
        $cachedData = $this->cache[$key] ?? null;
        if (null !== $cachedData) {
            return $cachedData;
        }

        $data = $callback();
        $this->cache[$key] = $data;

        return $data;
    }

    /** @return Google_Service_Drive_DriveFile[] */
    public function listDirectories(string $directoryId = null, int $limit = 1000): array
    {
        $cacheKey = json_encode([__METHOD__, $directoryId, $limit]);

        return $this->cached($cacheKey, function () use ($directoryId, $limit) {
            $service = new Google_Service_Drive($this->client);

            $query = [
                sprintf('mimeType = "%s"', self::FOLDER_MIME_TYPE),
            ];

            if (null !== $directoryId) {
                $query[] = sprintf('"%s" in parents', $directoryId);
            }

            $fileList = $service->files->listFiles([
                'fields' => self::FIELDS,
                'q' => implode(' AND ', $query),
                'pageSize' => $limit,
            ]);

            return $fileList->getFiles();
        });
    }

    /** @return Google_Service_Drive_DriveFile[] */
    public function listRootDirectories(int $limit = 1000): array
    {
        $cacheKey = json_encode([__METHOD__, $limit]);

        return $this->cached($cacheKey, function () use ($limit) {
            return $this->listDirectories($this->configuration->getRootDirectoryId(), $limit);
        });
    }

    /** @return Google_Service_Drive_DriveFile[] */
    public function listDocs(string $directoryId, int $limit = 1000): array
    {
        $cacheKey = json_encode([__METHOD__, $directoryId, $limit]);

        return $this->cached($cacheKey, function () use ($directoryId, $limit) {
            $service = new Google_Service_Drive($this->client);

            $query = [
                sprintf('mimeType = "%s"', self::DOC_MIME_TYPE),
                sprintf('"%s" in parents', $directoryId),
            ];

            $fileList = $service->files->listFiles([
                'fields' => self::FIELDS,
                'q' => implode(' AND ', $query),
                'pageSize' => $limit,
            ]);

            return $fileList->getFiles();
        });
    }

    /** @return Google_Service_Drive_DriveFile[] */
    public function listAllDocs(int $limit = 1000): array
    {
        $cacheKey = json_encode([__METHOD__, $limit]);

        return $this->cached($cacheKey, function () use ($limit) {
            $service = new Google_Service_Drive($this->client);

            $query = [
                sprintf('mimeType = "%s"', self::DOC_MIME_TYPE),
            ];

            $fileList = $service->files->listFiles([
                'fields' => self::FIELDS,
                'q' => implode(' AND ', $query),
                'pageSize' => $limit,
            ]);

            return $fileList->getFiles();
        });
    }

    public function getDocAsHTML(string $fileId): string
    {
        $cacheKey = json_encode([__METHOD__, $fileId]);

        return $this->cached($cacheKey, function () use ($fileId) {
            $service = new Google_Service_Drive($this->client);

            /** @var Response $file */
            $file = $service->files->export(
                $fileId,
                'text/html',
                [
                    'alt' => 'media',
                ]
            );

            return $file->getBody()->getContents();
        });
    }

    public function getFile(string $fileId): Google_Service_Drive_DriveFile
    {
        $cacheKey = json_encode([__METHOD__, $fileId]);

        return $this->cached($cacheKey, function () use ($fileId) {
            $service = new Google_Service_Drive($this->client);
            $file = $service->files->get($fileId,
                ['fields' => 'id, name, modifiedTime, parents, size']);

            return $file;
        });
    }
}
