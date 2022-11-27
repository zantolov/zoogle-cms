<?php

declare(strict_types=1);

namespace Zantolov\Zoogle\Cms\Service\Document;

use Zantolov\Zoogle\Cms\Service\GoogleDrive\Client\GoogleDriveClient;
use Zantolov\Zoogle\Model\Model\Document\Document;
use Zantolov\Zoogle\Model\Service\Converting\Converter;
use Zantolov\Zoogle\Model\Service\Processing\DocumentProcessingHub;

final class DocumentRepository
{
    public function __construct(
        private GoogleDriveClient $client,
        private Converter $converter,
        private DocumentProcessingHub $processor,
    ) {
    }

    /**
     * @todo make ID extraction logic from URL more robust
     */
    public function getByUrl(string $url): Document
    {
        \Safe\preg_match('/https:\/\/docs.google.com\/document\/d\/(.*)(?:\/edit)?$/U', $url, $result);
        $id = $result[1] ?? null;
        if (empty($id)) {
            throw new \InvalidArgumentException('Invalid URL given.');
        }

        return $this->getById($id);
    }

    public function getById(string $id): Document
    {
        $googleDoc = $this->client->getDoc($id);
        $doc = $this->converter->convert($googleDoc);

        return $this->processor->process($doc);
    }

    public function findByName(string $name): ?Document
    {
        $file = $this->client->findByName($name);
        if ($file === null) {
            return null;
        }

        return $this->getById($file->getId());
    }
}
