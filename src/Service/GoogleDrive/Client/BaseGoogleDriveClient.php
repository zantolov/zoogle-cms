<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Service\GoogleDrive\Client;

use Google_Client;
use Google_Service_Docs;
use Google_Service_Drive;
use GuzzleHttp\Psr7\Response;
use Zantolov\ZoogleCms\Service\GoogleDrive\Configuration\Configuration;

class BaseGoogleDriveClient implements GoogleDriveClient
{
    private const FIELDS = 'files(*)';
    private const DOC_MIME_TYPE = 'application/vnd.google-apps.document';
    private const FOLDER_MIME_TYPE = 'application/vnd.google-apps.folder';

    private Google_Client $client;
    private array $cache = [];

    public function __construct(private GoogleDriveAuth $auth, private Configuration $configuration)
    {
        $this->initializeClient();
    }

    private function initializeClient(): void
    {
        $this->client = new Google_Client();
        $this->client->setApplicationName("Client_Library_Examples");
        $this->client->setScopes([
            Google_Service_Drive::DRIVE_READONLY,
            Google_Service_Docs::DOCUMENTS_READONLY
        ]);
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

    /** @return \Google_Service_Drive_DriveFile[] */
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

    /** @return \Google_Service_Drive_DriveFile[] */
    public function listRootDirectories(int $limit = 1000): array
    {
        $cacheKey = json_encode([__METHOD__, $limit]);

        return $this->cached($cacheKey, function () use ($limit) {
            return $this->listDirectories($this->configuration->rootDirectoryId, $limit);
        });
    }

    /** @return \Google_Service_Drive_DriveFile[] */
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

    /** @return \Google_Service_Drive_DriveFile[] */
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

    /** @return \Google_Service_Drive_DriveFile[] */
    public function searchDocs(string $query, int $limit = 1000): array
    {
        $cacheKey = json_encode([__METHOD__, $limit, $query]);

        return $this->cached($cacheKey, function () use ($query, $limit) {
            $service = new Google_Service_Drive($this->client);

            $query = [
                sprintf('mimeType = "%s"', self::DOC_MIME_TYPE),
                sprintf('(name contains "%s" OR fullText contains "%s")', $query, $query),
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

            return $file->getBody()?->getContents();
        });
    }

    public function getFile(string $fileId): \Google_Service_Drive_DriveFile
    {
        $cacheKey = json_encode([__METHOD__, $fileId]);

        return $this->cached($cacheKey, function () use ($fileId) {
            $service = new Google_Service_Drive($this->client);
            $file = $service->files->get(
                $fileId,
                ['fields' => 'id, name, modifiedTime, parents, size']
            );

            return $file;
        });
    }

    public function getDoc(string $fileId): \Google_Service_Docs_Document
    {
        $service = new \Google_Service_Docs($this->client);

        return $service->documents->get($fileId,);
    }

    /**
     * @todo optimise searching by leveraging the search feature
     */
    public function findByName(string $name): ?\Google_Service_Drive_DriveFile
    {
        foreach ($this->listAllDocs() as $file) {
            if ($file->getName() === $name) {
                return $file;
            }
        }

        return null;
    }
}
