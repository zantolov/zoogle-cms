<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDrive\Content\Document;

use Zantolov\ZoogleCms\Domain\Document\Document;
use Zantolov\ZoogleCms\Infrastructure\GoogleDrive\Client\GoogleDriveClient;
use Zantolov\ZoogleCms\Infrastructure\GoogleDrive\Content\Document\Converting\Converter;
use Zantolov\ZoogleCms\Infrastructure\GoogleDrive\Content\Document\Processing\DocumentProcessingHub;

class DocumentFactory
{
    public function __construct(
        private GoogleDriveClient $client,
        private Converter $converter,
        private DocumentProcessingHub $processor,
    )
    {
    }

    public function fromUrl(string $url): Document
    {
        preg_match('/https:\/\/docs.google.com\/document\/d\/(.*)(?:\/edit)?$/U', $url, $result);
        $id = $result[1] ?? null;
        if (empty($id)) {
            throw new \InvalidArgumentException();
        }

        $googleDoc = $this->client->getDoc($id);
        $doc = $this->converter->convert($googleDoc);
        $doc = $this->processor->process($doc);

        return $doc;
    }
}
