<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Client;

use GuzzleHttp\Psr7\Response;
use Zantolov\ZoogleCms\Configuration\Configuration;

class BaseGoogleDriveClient implements GoogleDriveClient
{
    private const string FIELDS = 'files(*)';

    private const string DOC_MIME_TYPE = 'application/vnd.google-apps.document';

    private const string FOLDER_MIME_TYPE = 'application/vnd.google-apps.folder';

    private \Google_Client $client;

    /**
     * @var array<string, mixed>
     */
    private array $cache = [];

    public function __construct(private readonly GoogleDriveAuth $auth, private readonly Configuration $configuration)
    {
        $this->initializeClient();
    }

    private function initializeClient(): void
    {
        $this->client = new \Google_Client();
        $this->client->setApplicationName('Client_Library_Examples');
        $this->client->setScopes([
            \Google_Service_Drive::DRIVE_READONLY,
            \Google_Service_Docs::DOCUMENTS_READONLY,
        ]);
        $this->client->setClientId($this->auth->getClientId());
        $this->client->setAuthConfig($this->auth->getAuthConfig());
    }

    private function cached(string $key, callable $callback): mixed
    {
        $cachedData = $this->cache[$key] ?? null;
        if ($cachedData !== null) {
            return $cachedData;
        }

        $data = $callback();
        $this->cache[$key] = $data;

        return $data;
    }

    /** @return \Google_Service_Drive_DriveFile[] */
    public function listDirectories(?string $directoryId = null, int $limit = 1000): array
    {
        $cacheKey = json_encode([__METHOD__, $directoryId, $limit]);

        return $this->cached($cacheKey, function () use ($directoryId, $limit): void { /** @phpstan-ignore-line return.type */
            $service = new \Google_Service_Drive($this->client);

            $query = [
                sprintf('mimeType = "%s"', self::FOLDER_MIME_TYPE),
            ];

            if ($directoryId !== null) {
                $query[] = sprintf('"%s" in parents', $directoryId);
            }

            $fileList = $service->files->listFiles([ // @phpstan-ignore-line method.nonObject
                'fields' => self::FIELDS,
                'q' => implode(' AND ', $query),
                'pageSize' => $limit,
            ]);

            return $fileList->getFiles(); // @phpstan-ignore-line method.nonObject
        });
    }

    /** @return \Google_Service_Drive_DriveFile[] */
    public function listRootDirectories(int $limit = 1000): array
    {
        $cacheKey = json_encode([__METHOD__, $limit]);

        assert(is_string($cacheKey));

        return $this->cached($cacheKey, fn (): array => $this->listDirectories($this->configuration->rootDirectoryId, $limit)); // @phpstan-ignore-line
    }

    /** @return \Google_Service_Drive_DriveFile[] */
    public function listDocs(string $directoryId, int $limit = 1000): array
    {
        $cacheKey = json_encode([__METHOD__, $directoryId, $limit]);

        return $this->cached($cacheKey, function () use ($directoryId, $limit) { /** @phpstan-ignore-line */
            $service = new \Google_Service_Drive($this->client);

            $query = [
                sprintf('mimeType = "%s"', self::DOC_MIME_TYPE),
                sprintf('"%s" in parents', $directoryId),
            ];

            $fileList = $service->files->listFiles([ // @phpstan-ignore-line method.nonObject
                'fields' => self::FIELDS,
                'q' => implode(' AND ', $query),
                'pageSize' => $limit,
            ]);

            return $fileList->getFiles(); // @phpstan-ignore-line method.nonObject
        });
    }

    /** @return \Google_Service_Drive_DriveFile[] */
    public function listAllDocs(int $limit = 1000): array
    {
        $cacheKey = json_encode([__METHOD__, $limit]);

        return $this->cached($cacheKey, function () use ($limit) { /** @phpstan-ignore-line */
            $service = new \Google_Service_Drive($this->client);

            $query = [
                sprintf('mimeType = "%s"', self::DOC_MIME_TYPE),
            ];

            $fileList = $service->files->listFiles([ // @phpstan-ignore-line method.nonObject
                'fields' => self::FIELDS,
                'q' => implode(' AND ', $query),
                'pageSize' => $limit,
            ]);

            return $fileList->getFiles(); // @phpstan-ignore-line method.nonObject
        });
    }

    /** @return \Google_Service_Drive_DriveFile[] */
    public function searchDocs(string $query, int $limit = 1000): array
    {
        $cacheKey = json_encode([__METHOD__, $limit, $query]);
        assert(is_string($cacheKey));

        return $this->cached($cacheKey, function () use ($query, $limit) { /** @phpstan-ignore-line */
            $service = new \Google_Service_Drive($this->client);

            $query = [
                sprintf('mimeType = "%s"', self::DOC_MIME_TYPE),
                sprintf('(name contains "%s" OR fullText contains "%s")', $query, $query),
            ];

            $fileList = $service->files->listFiles([ // @phpstan-ignore-line method.nonObject
                'fields' => self::FIELDS,
                'q' => implode(' AND ', $query),
                'pageSize' => $limit,
            ]);

            return $fileList->getFiles(); // @phpstan-ignore-line method.nonObject
        });
    }

    public function getDocAsHTML(string $fileId): string
    {
        $cacheKey = json_encode([__METHOD__, $fileId]);

        return $this->cached($cacheKey, function () use ($fileId) { /** @phpstan-ignore-line */
            $service = new \Google_Service_Drive($this->client);

            /** @var Response $file */
            $file = $service->files->export( // @phpstan-ignore-line method.nonObject
                $fileId,
                'text/html',
                [
                    'alt' => 'media',
                ],
            );

            return $file->getBody()->getContents();
        });
    }

    public function getFile(string $fileId): \Google_Service_Drive_DriveFile
    {
        $cacheKey = json_encode([__METHOD__, $fileId]);

        return $this->cached($cacheKey, function () use ($fileId) { /** @phpstan-ignore-line */
            $service = new \Google_Service_Drive($this->client);

            return $service->files->get(// @phpstan-ignore-line method.nonObject
                $fileId,
                ['fields' => 'id, name, modifiedTime, parents, size'],
            );
        });
    }

    public function getDoc(string $fileId): \Google_Service_Docs_Document
    {
        $service = new \Google_Service_Docs($this->client);
        $file = $service->documents->get($fileId); // @phpstan-ignore-line method.nonObject
        assert($file instanceof \Google_Service_Docs_Document);

        return $file;
    }
}
