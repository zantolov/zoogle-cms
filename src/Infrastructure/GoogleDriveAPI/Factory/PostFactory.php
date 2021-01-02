<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Factory;

use DateTimeImmutable;
use Exception;
use Zantolov\ZoogleCms\Domain\Post\Author;
use Zantolov\ZoogleCms\Domain\Post\Post;
use Zantolov\ZoogleCms\Domain\Post\PostId;
use Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Client\GoogleDriveClient;
use Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Content\Converting\GoogleDocs\Converter;
use Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Content\Converting\HtmlConverter;

class PostFactory
{
    public function __construct(
        private CategoryFactory $categoryFactory,
        private Converter $documentConverter,
        private HtmlConverter $htmlConverter,
        private GoogleDriveClient $client
    ) {
    }

    /**
     * @throws Exception
     */
    public function make(\Google_Service_Drive_DriveFile $file): Post
    {
        $parentFolderId = $file['parents'][0] ?? null;
        $category = $parentFolderId
            ? $this->categoryFactory->fromGoogleDriveFileId($parentFolderId)
            : null;

        $doc = $this->client->getDoc($file->getId());
        $document = $this->documentConverter->convert($doc);
        $html = $this->htmlConverter->convert($document);

        $leadingImage = $document->getImages()[0] ?? null;
        $metadata = $document->getMetadata();
        $publishDateTime = $metadata->has('publish') ? new DateTimeImmutable($metadata->get('publish')) : null;
        $author = $metadata->has('author') ? new Author($metadata->get('author')) : null;

        return new Post(
            new PostId($file->getId()),
            $document->getTitle()->toString(),
            $file->getName(),
            $html,
            $publishDateTime,
            $leadingImage?->src,
            $category,
            $author
        );
    }
}
