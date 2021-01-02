<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Factory;

use Cocur\Chain\Chain;
use DateTimeImmutable;
use Exception;
use Zantolov\ZoogleCms\Domain\Document\Document;
use Zantolov\ZoogleCms\Domain\Document\DocumentElement;
use Zantolov\ZoogleCms\Domain\Document\Metadata;
use Zantolov\ZoogleCms\Domain\Document\Title;
use Zantolov\ZoogleCms\Domain\Post\Author;
use Zantolov\ZoogleCms\Domain\Post\Post;
use Zantolov\ZoogleCms\Domain\Post\PostId;
use Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Client\GoogleDriveClient;
use Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Content\Converting\Document\Converter;
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

        $googleDocument = $this->client->getDoc($file->getId());
        $document = $this->documentConverter->convert($googleDocument);

        // Extract the needed elements from the document that will be rendered independently.
        // And remove them from the rendering document
        $leadingImage = $document->firstImage();
        $metadata = $document->getMetadata();
        $title = $document->getTitle();
        $items = Chain::create($document->items)
            ->filter(fn (DocumentElement $element) => !$element instanceof Title || !$element instanceof Metadata)
            ->filter(fn (DocumentElement $element) => $element !== $document->firstImage())
            ->values()
            ->array;
        $renderingDocument = new Document($items);
        $html = $this->htmlConverter->convert($renderingDocument);

        $publishDateTime = $metadata->has('publish') ? new DateTimeImmutable($metadata->get('publish')) : null;
        $author = $metadata->has('author') ? new Author($metadata->get('author')) : null;

        return new Post(
            new PostId($file->getId()),
            $title->toString(),
            $file->getName(),
            $html,
            $publishDateTime,
            $leadingImage?->src,
            $category,
            $author
        );
    }
}
