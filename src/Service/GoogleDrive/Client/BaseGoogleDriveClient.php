<?php

declare(strict_types=1);

namespace Zantolov\Zoogle\Cms\Service\GoogleDrive\Client;

use Google\Client;
use Google\Service\Docs;
use Google\Service\Docs\Document;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use GuzzleHttp\Psr7\Response;
use Zantolov\Zoogle\Cms\Service\GoogleDrive\Configuration\Configuration;

final class BaseGoogleDriveClient implements GoogleDriveClient
{
    private const FIELDS = 'files(*)';
    private const DOC_MIME_TYPE = 'application/vnd.google-apps.document';
    private const FOLDER_MIME_TYPE = 'application/vnd.google-apps.folder';

    private Client $client;

    /**
     * @var array<string, mixed>
     */
    private array $cache = [];

    public function __construct(private GoogleDriveAuth $auth, private Configuration $configuration)
    {
        $this->initializeClient();
    }

    private function initializeClient(): void
    {
        $this->client = new Client();
        $this->client->setApplicationName('Zoogle CMS');
        $this->client->setScopes([
            Drive::DRIVE_READONLY,
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

    /** @return DriveFile[] */
    public function listDirectories(string $directoryId = null, int $limit = 1000): array
    {
        $cacheKey = \Safe\json_encode([__METHOD__, $directoryId, $limit]);

        return $this->cached($cacheKey, function () use ($directoryId, $limit) {
            $service = new Drive($this->client);

            $query = [
                \Safe\sprintf('mimeType = "%s"', self::FOLDER_MIME_TYPE),
            ];

            if ($directoryId !== null) {
                $query[] = \Safe\sprintf('"%s" in parents', $directoryId);
            }

            $fileList = $service->files->listFiles([
                'fields' => self::FIELDS,
                'q' => implode(' AND ', $query),
                'pageSize' => $limit,
            ]);

            return $fileList->getFiles();
        });
    }

    /** @return DriveFile[] */
    public function listRootDirectories(int $limit = 1000): array
    {
        $cacheKey = \Safe\json_encode([__METHOD__, $limit]);

        return $this->cached(
            $cacheKey,
            fn (): array => $this->listDirectories($this->configuration->rootDirectoryId, $limit)
        );
    }

    /** @return DriveFile[] */
    public function listDocs(string $directoryId, int $limit = 1000): array
    {
        $cacheKey = \Safe\json_encode([__METHOD__, $directoryId, $limit]);

        return $this->cached($cacheKey, function () use ($directoryId, $limit): array {
            $service = new Drive($this->client);

            $query = [
                \Safe\sprintf('mimeType = "%s"', self::DOC_MIME_TYPE),
                \Safe\sprintf('"%s" in parents', $directoryId),
            ];

            $fileList = $service->files->listFiles([
                'fields' => self::FIELDS,
                'q' => implode(' AND ', $query),
                'pageSize' => $limit,
            ]);

            return $fileList->getFiles();
        });
    }

    /** @return DriveFile[] */
    public function listAllDocs(int $limit = 1000): array
    {
        $cacheKey = \Safe\json_encode([__METHOD__, $limit]);

        return $this->cached($cacheKey, function () use ($limit): array {
            $service = new Drive($this->client);

            $query = [
                \Safe\sprintf('mimeType = "%s"', self::DOC_MIME_TYPE),
            ];

            $fileList = $service->files->listFiles([
                'fields' => self::FIELDS,
                'q' => implode(' AND ', $query),
                'pageSize' => $limit,
            ]);

            return $fileList->getFiles();
        });
    }

    /** @return DriveFile[] */
    public function searchDocs(string $query, int $limit = 1000): array
    {
        $cacheKey = \Safe\json_encode([__METHOD__, $limit, $query]);

        return $this->cached($cacheKey, function () use ($query, $limit): array {
            $service = new Drive($this->client);

            $query = [
                \Safe\sprintf('mimeType = "%s"', self::DOC_MIME_TYPE),
                \Safe\sprintf('(name contains "%s" OR fullText contains "%s")', $query, $query),
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
        $cacheKey = \Safe\json_encode([__METHOD__, $fileId]);

        return $this->cached($cacheKey, function () use ($fileId): string {
            $service = new Drive($this->client);

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

    public function getFile(string $fileId): DriveFile
    {
        $cacheKey = \Safe\json_encode([__METHOD__, $fileId]);

        return $this->cached($cacheKey, function () use ($fileId) {
            $service = new Drive($this->client);

            return $service->files->get(
                $fileId,
                ['fields' => 'id, name, modifiedTime, parents, size']
            );
        });
    }

    public function getDoc(string $fileId): Document
    {
        $service = new Docs($this->client);

        return $service->documents->get($fileId);
    }

    /**
     * @todo optimise searching by leveraging the search feature
     */
    public function findByName(string $name): ?DriveFile
    {
        foreach ($this->listAllDocs() as $file) {
            if ($file->getName() === $name) {
                return $file;
            }
        }

        return null;
    }
}
