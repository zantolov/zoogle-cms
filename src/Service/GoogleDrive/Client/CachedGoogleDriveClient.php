<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Service\GoogleDrive\Client;

use Google\Service\Docs\Document;
use Google\Service\Drive\DriveFile;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final class CachedGoogleDriveClient implements GoogleDriveClient
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

    /** @return DriveFile[] */
    public function listDirectories(string $directoryId = null, int $limit = 1000): array
    {
        $key = 'listDirectories.dir_'.$directoryId.'.limit_'.$limit;

        return $this->cache->get($key, function (ItemInterface $item) use ($directoryId, $limit) {
            if ($directoryId) {
                $item->tag($this->dirCacheTag($directoryId));
            }

            $data = $this->client->listDirectories($directoryId, $limit);
            $item->set($data);
            $item->tag($this->commonCacheTag());

            return $data;
        });
    }

    /** @return DriveFile[] */
    public function listRootDirectories(int $limit = 1000): array
    {
        $key = 'listRootDirectories.limit_'.$limit;

        return $this->cache->get($key, function (ItemInterface $item) use ($limit) {
            $data = $this->client->listRootDirectories($limit);
            $item->set($data);
            $item->tag($this->commonCacheTag());

            return $data;
        });
    }

    /** @return DriveFile[] */
    public function listDocs(string $directoryId, int $limit = 1000): array
    {
        $key = 'listDocs.dir_'.$directoryId.'.limit_'.$limit;

        return $this->cache->get($key, function (ItemInterface $item) use ($directoryId, $limit) {
            if ($directoryId) {
                $item->tag($this->dirCacheTag($directoryId));
            }

            $data = $this->client->listDocs($directoryId, $limit);
            $item->set($data);
            $item->tag($this->commonCacheTag());

            foreach ($data as $file) {
                $item->tag($this->fileCacheTag($file->getId()));
            }

            return $data;
        });
    }

    /** @return DriveFile[] */
    public function listAllDocs(int $limit = 1000): array
    {
        $key = 'listAllDocs.limit_'.$limit;

        return $this->cache->get($key, function (ItemInterface $item) use ($limit) {
            $data = $this->client->listAllDocs($limit);
            $item->set($data);
            $item->tag($this->commonCacheTag());

            foreach ($data as $file) {
                $item->tag($this->fileCacheTag($file->getId()));
            }

            return $data;
        });
    }

    /** @return DriveFile[] */
    public function searchDocs(string $query, int $limit = 1000): array
    {
        $key = 'listAllDocs.limit_'.$limit.'.query_'.$query;

        return $this->cache->get($key, function (ItemInterface $item) use ($query, $limit) {
            $data = $this->client->searchDocs($query, $limit);
            $item->set($data);
            $item->tag($this->commonCacheTag());

            foreach ($data as $file) {
                $item->tag($this->fileCacheTag($file->getId()));
            }

            return $data;
        });
    }

    public function getDoc(string $fileId): Document
    {
        $key = 'getDoc.file_'.$fileId;

        return $this->cache->get($key, function (ItemInterface $item) use ($fileId) {
            $data = $this->client->getDoc($fileId);
            $item->tag($this->fileCacheTag($fileId));
            $item->tag($this->commonCacheTag());
            $item->set($data);

            return $data;
        });
    }

    public function getDocAsHTML(string $fileId): string
    {
        $key = 'getDocAsHTML.fileHtml_'.$fileId;

        return $this->cache->get($key, function (ItemInterface $item) use ($fileId) {
            $item->tag($this->fileCacheTag($fileId));
            $item->tag($this->commonCacheTag());
            $data = $this->client->getDocAsHTML($fileId);
            $item->set($data);

            return $data;
        });
    }

    public function getFile(string $fileId): DriveFile
    {
        $key = 'getFile.file_'.$fileId;

        return $this->cache->get($key, function (ItemInterface $item) use ($fileId) {
            $item->tag($this->fileCacheTag($fileId));
            $item->tag($this->commonCacheTag());
            $data = $this->client->getFile($fileId);
            $item->set($data);

            return $data;
        });
    }

    public function findByName(string $name): ?DriveFile
    {
        $key = 'getFile.file_name_'.$name;

        return $this->cache->get($key, function (ItemInterface $item) use ($name) {
            $item->tag($this->fileCacheTag('name_'.$name));
            $item->tag($this->commonCacheTag());
            $data = $this->client->findByName($name);
            $item->set($data);

            return $data;
        });
    }
}
