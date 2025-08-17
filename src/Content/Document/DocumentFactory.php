<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Content\Document;

use Zantolov\Zoogle\Model\Model\Document\Document;
use Zantolov\Zoogle\Model\Service\Converting\Converter;
use Zantolov\Zoogle\Model\Service\Processing\DocumentProcessingHub;
use Zantolov\ZoogleCms\Client\GoogleDriveClient;

class DocumentFactory
{
    public function __construct(
        private readonly GoogleDriveClient $client,
        private readonly Converter $converter,
        private readonly DocumentProcessingHub $processor,
    ) {
    }

    public function fromUrl(string $url): Document
    {
        preg_match('/https:\/\/docs.google.com\/document\/d\/(.*)(?:\/edit)?$/U', $url, $result);
        $id = $result[1] ?? null;
        if ($id === null || $id === '' || $id === '0') {
            throw new \InvalidArgumentException();
        }

        return $this->fromId($id);
    }

    public function fromId(string $id): Document
    {
        $googleDoc = $this->client->getDoc($id);
        $doc = $this->converter->convert($googleDoc);
        $doc = $this->processor->process($doc);

        return $doc;
    }
}
