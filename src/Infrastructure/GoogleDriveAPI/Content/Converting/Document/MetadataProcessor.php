<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Content\Converting\Document;

use Zantolov\ZoogleCms\Domain\Document\DocumentElement;
use Zantolov\ZoogleCms\Domain\Document\Metadata;

class MetadataProcessor
{
    public function __construct(private ContentConverter $converter)
    {
    }

    /**
     * Extract key-value information from the document header.
     */
    public function processMetadata(\Google_Service_Docs_Document $doc): Metadata
    {
        $headers = $doc->getHeaders();
        $header = array_values($headers)[0] ?? null;
        if (null === $header) {
            return new Metadata([]);
        }

        $meta = [];
        $items = $header->getContent();
        $items = array_map(
            fn (\Google_Service_Docs_StructuralElement $element) => array_reduce(
                $this->generateElements($element),
                fn (string $carry, DocumentElement $element) => $carry.$element->toString(),
                ''
            ),
            $items
        );

        foreach ($items as $item) {
            $components = preg_split('/[\n\v]+/', $item);
            $components = array_filter($components);
            $keyValues = array_map(
                fn (string $line) => explode(':', $line, 2),
                $components
            );
            foreach ($keyValues as $keyValue) {
                $meta[mb_strtolower(trim($keyValue[0]))] = trim($keyValue[1] ?? null);
            }
        }

        return new Metadata($meta);
    }

    private function generateElements(\Google_Service_Docs_StructuralElement $element): array
    {
        $elements = [];
        if ($paragraph = $element->getParagraph()) {
            return $this->converter->convert($paragraph);
        }

        return $elements;
    }
}
