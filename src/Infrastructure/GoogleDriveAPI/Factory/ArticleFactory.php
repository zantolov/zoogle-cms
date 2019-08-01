<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Factory;

use DateTimeImmutable;
use Exception;
use Google_Service_Drive_DriveFile;
use Zantolov\ZoogleCms\Domain\Article as ArticleInterface;
use Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Client\GoogleDriveClient;
use Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\ContentProcessing\MetadataProcessor;
use Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\ContentProcessing\StyleRemover;
use Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Model\Article;
use Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Model\ArticleId;

class ArticleFactory
{
    /** @var GoogleDriveClient */
    private $client;

    /** @var MetadataProcessor */
    private $metadataReader;

    public function __construct(GoogleDriveClient $client, MetadataProcessor $metadataReader)
    {
        $this->client = $client;
        $this->metadataReader = $metadataReader;
    }

    /**
     * @throws Exception
     */
    public function make(Google_Service_Drive_DriveFile $file): ArticleInterface
    {
        $html = $this->client->getDocAsHTML($file->getId());
        $title = $this->metadataReader->extractTitle($html);
        $meta = $this->metadataReader->extractMeta($html);
        $leadingImageUrl = $this->metadataReader->extractFirstImageUrl($html);

        $authorCaption = $meta['author'] ?? null;

        $tags = explode(',', $meta['tags'] ?? '');
        foreach ($tags as &$tag) {
            $tag = trim($tag);
        }

        $publishDateTime = null;
        if (true === array_key_exists('publish', $meta)) {
            $publishDateTime = new DateTimeImmutable($meta['publish']);
        }

        // @todo add support for modifiying the article body
        // @todo handle header metadata
        // @todo handle ignored area
        $processors = [];
        $processors[] = new StyleRemover();

        foreach ($processors as $processor) {
            $html = $processor->process($html);
        }

        return new Article(
            new ArticleId($file->getId()),
            $title,
            $file->getName(),
            $html,
            $publishDateTime,
            $authorCaption,
            $leadingImageUrl,
            $tags
        );
    }
}
