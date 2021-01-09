<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDrive\Client;

use Google_Service_Drive_DriveFile;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class CachedGoogleDriveClient implements GoogleDriveClient
{
    public function __construct(private GoogleDriveClient $client, private TagAwareCacheInterface $cache)
    {
    }

    private function commonCacheTag(): string
    {
        return 'zoogle';
    }

    private function fileCacheTag(string $fileId): string
    {
        return 'file_'.$fileId;
    }

    public function dirCacheTag(string $dirId): string
    {
        return 'dir_'.$dirId;
    }

    public function invalidateCache(): void
    {
        $this->cache->invalidateTags([$this->commonCacheTag()]);
    }

    public function invalidateDirCache(string $dir): void
    {
        $this->cache->invalidateTags([$this->dirCacheTag($dir)]);
    }

    public function invalidateFileCache(string $fileId): void
    {
        $this->cache->invalidateTags([$this->fileCacheTag($fileId)]);
    }

    /** @return Google_Service_Drive_DriveFile[] */
    public function listDirectories(string $directoryId = null, int $limit = 1000): array
    {
        $key = 'listDirectories.dir_'.$directoryId.'.limit_'.$limit;

        return $this->cache->get($key, function (ItemInterface $item) use ($directoryId, $limit) {
            if ($directoryId) {
                $item->tag($this->dirCacheTag($directoryId));
            }

            $data = $this->client->listDirectories($directoryId, $limit);
            $item->set($data);
            $item->expiresAfter(new \DateInterval('PT1H'));
            $item->tag($this->commonCacheTag());

            return $data;
        });
    }

    /** @return Google_Service_Drive_DriveFile[] */
    public function listRootDirectories(int $limit = 1000): array
    {
        $key = 'listRootDirectories.limit_'.$limit;

        return $this->cache->get($key, function (ItemInterface $item) use ($limit) {
            $data = $this->client->listRootDirectories($limit);
            $item->set($data);
            $item->expiresAfter(new \DateInterval('PT1H'));
            $item->tag($this->commonCacheTag());

            return $data;
        });
    }

    /** @return Google_Service_Drive_DriveFile[] */
    public function listDocs(string $directoryId, int $limit = 1000): array
    {
        $key = 'listDocs.dir_'.$directoryId.'.limit_'.$limit;

        return $this->cache->get($key, function (ItemInterface $item) use ($directoryId, $limit) {
            if ($directoryId) {
                $item->tag($this->dirCacheTag($directoryId));
            }

            $data = $this->client->listDocs($directoryId, $limit);
            $item->set($data);
            $item->expiresAfter(new \DateInterval('PT1H'));
            $item->tag($this->commonCacheTag());

            foreach ($data as $file) {
                $item->tag($this->fileCacheTag($file->getId()));
            }

            return $data;
        });
    }

    /** @return Google_Service_Drive_DriveFile[] */
    public function listAllDocs(int $limit = 1000): array
    {
        $key = 'listAllDocs.limit_'.$limit;

        return $this->cache->get($key, function (ItemInterface $item) use ($limit) {
            $data = $this->client->listAllDocs($limit);
            $item->set($data);
            $item->expiresAfter(new \DateInterval('PT1H'));
            $item->tag($this->commonCacheTag());

            foreach ($data as $file) {
                $item->tag($this->fileCacheTag($file->getId()));
            }

            return $data;
        });
    }

    /** @return Google_Service_Drive_DriveFile[] */
    public function searchDocs(string $query, int $limit = 1000): array
    {
        $key = 'listAllDocs.limit_'.$limit.'.query_'.$query;

        return $this->cache->get($key, function (ItemInterface $item) use ($query, $limit) {
            $data = $this->client->searchDocs($query, $limit);
            $item->set($data);
            $item->expiresAfter(new \DateInterval('PT1H'));
            $item->tag($this->commonCacheTag());

            foreach ($data as $file) {
                $item->tag($this->fileCacheTag($file->getId()));
            }

            return $data;
        });
    }

    public function getDoc(string $fileId): \Google_Service_Docs_Document
    {
        $key = 'getDoc.file_'.$fileId;

        return $this->cache->get($key, function (ItemInterface $item) use ($fileId) {
            $data = $this->client->getDoc($fileId);
            $item->tag($this->fileCacheTag($fileId));
            $item->set($data);
            $item->expiresAfter(new \DateInterval('PT1H'));
            $item->tag($this->commonCacheTag());

            return $data;
        });
    }

    public function getDocAsHTML(string $fileId): string
    {
        $key = 'getDocAsHTML.fileHtml_'.$fileId;

        return $this->cache->get($key, function (ItemInterface $item) use ($fileId) {
            $item->tag($this->fileCacheTag($fileId));
            $data = $this->client->getDocAsHTML($fileId);
            $item->set($data);
            $item->expiresAfter(new \DateInterval('PT1H'));

            return $data;
        });
    }

    public function getFile(string $fileId): \Google_Service_Drive_DriveFile
    {
        $key = 'getFile.file_'.$fileId;

        return $this->cache->get($key, function (ItemInterface $item) use ($fileId) {
            $item->tag($this->fileCacheTag($fileId));
            $data = $this->client->getFile($fileId);
            $item->set($data);
            $item->expiresAfter(new \DateInterval('PT1H'));
            $item->tag($this->commonCacheTag());

            return $data;
        });
    }
}
