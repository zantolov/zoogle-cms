<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Factory;

use DateTimeImmutable;
use Exception;
use Google_Service_Drive_DriveFile;
use Psr\Cache\CacheItemPoolInterface;
use Zantolov\ZoogleCms\Domain\Post\Author;
use Zantolov\ZoogleCms\Domain\Post\Post;
use Zantolov\ZoogleCms\Domain\Post\PostId;
use Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Client\GoogleDriveClient;
use Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\ContentProcessing\MetadataProcessor;
use Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\ContentProcessing\StyleRemover;

class ArticleFactory
{
    private GoogleDriveClient $client;
    private MetadataProcessor $metadataReader;
    private CacheItemPoolInterface $cache;

    public function __construct(
        GoogleDriveClient $client,
        MetadataProcessor $metadataReader,
        CacheItemPoolInterface $cache
    ) {
        $this->client = $client;
        $this->metadataReader = $metadataReader;
        $this->cache = $cache;
    }

    /**
     * @throws Exception
     */
    public function make(Google_Service_Drive_DriveFile $file): Post
    {
        $html = $this->loadFileContent($file);
        $title = $this->metadataReader->extractTitle($html);
        $metadata = $this->metadataReader->extractMeta($html);
        $leadingImageUrl = $this->metadataReader->extractFirstImageUrl($html);
        $html = (new StyleRemover())->process($html);

        $publishDateTime = $metadata->has('publish') ? new DateTimeImmutable($metadata->get('publish')) : null;
        $author = $metadata->has('author') ? new Author($metadata->get('author')) : null;

        return new Post(
            new PostId($file->getId()),
            $title,
            $file->getName(),
            $html,
            $publishDateTime,
            $leadingImageUrl,
            $author
        );
    }

    private function loadFileContent(Google_Service_Drive_DriveFile $file): string
    {
        $item = $this->cache->getItem($file->id);
        $isModified = true;

        // Check if the cached version of the file has been modified on the upstream server
        // by comparing the modified_at timestamp of the cached file and the $modifiedTime prop of Google Drive file
        if (true === $item->isHit()) {
            $data = $item->get();
            $modifiedTime = $file->getModifiedTime() !== null ? new DateTimeImmutable($file->getModifiedTime()) : null;
            $cachedModifiedDate = $data['modified_at'] !== null ? new DateTimeImmutable($data['modified_at']) : null;

            $isModified = $modifiedTime !== null
                && $cachedModifiedDate !== null
                && $cachedModifiedDate < $modifiedTime;
        }

        if (false === $item->isHit() || true === $isModified) {
            $item->set([
                'html' => $this->client->getDocAsHTML($file->getId()),
                'modified_at' => $file->getModifiedTime(),
            ]);
            $this->cache->save($item);
        }

        return $item->get()['html'];
    }
}
