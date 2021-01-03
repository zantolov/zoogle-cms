<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDrive\Content\Document\Converting;

use Google_Service_Docs_Document;
use Zantolov\ZoogleCms\Domain\Document\Document;
use Zantolov\ZoogleCms\Domain\Document\DocumentElement;
use Zantolov\ZoogleCms\Domain\Document\DocumentList;
use Zantolov\ZoogleCms\Domain\Document\DocumentObject;
use Zantolov\ZoogleCms\Domain\Document\Metadata;

class Converter
{
    /**
     * @param iterable<ElementConverter> $converters
     */
    public function __construct(
        private iterable $converters
    ) {
    }

    public function convert(Google_Service_Docs_Document $document): Document
    {
        $elements = [];
        $elements[] = $this->generateMetadata($document);
        $lists = $this->generateDocumentLists($document);
        $objects = $this->generateDocumentObjects($document);

        /** @var \Google_Service_Docs_StructuralElement $element */
        foreach ($document->getBody() as $element) {
            $elements = [...$elements, ...$this->generateElements($element)];
        }

        return new Document($document->getDocumentId(), $elements, $lists, $objects);
    }

    private function generateElements(\Google_Service_Docs_StructuralElement $element): array
    {
        $elements = [];
        if ($paragraph = $element->getParagraph()) {
            foreach ($this->converters as $converter) {
                if ($converter->supports($paragraph)) {
                    $elements = [...$elements, ...$converter->convert($paragraph)];
                }
            }
        }

        if ($element->getTable()) {
            // @todo
        }

        if ($element->getSectionBreak()) {
            // @todo
        }

        return $elements;
    }

    private function generateMetadata(\Google_Service_Docs_Document $doc): Metadata
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
                array_column($this->generateElements($element), 0),
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

    private function generateDocumentLists(\Google_Service_Docs_Document $document): array
    {
        $lists = [];
        foreach ($document->getLists() as $listId => $list) {
            $listProperties = $list->getListProperties();
            $nestingLevels = $listProperties?->getNestingLevels() ?? [];
            $firstNestingLevel = $nestingLevels[0] ?? null;

            $listType = match ($firstNestingLevel?->getGlyphType()) {
                'DECIMAL' => DocumentList::TYPE_ORDERED,
                default => DocumentList::TYPE_UNORDERED
            };

            $lists[] = new DocumentList($listId, [], $listType);
        }

        return $lists;
    }

    private function generateDocumentObjects(\Google_Service_Docs_Document $document): array
    {
        $objects = [];
        /** @var  \Google_Service_Docs_InlineObject $documentObject */
        foreach ($document->getInlineObjects() as $id => $documentObject) {
            if ($documentObject->getInlineObjectProperties()?->getEmbeddedObject()) {
                $embeddedObject = $documentObject->getInlineObjectProperties()?->getEmbeddedObject();

                $imageSrc = $embeddedObject?->getImageProperties()?->getContentUri();

                // @todo add support for cropped images
                // @todo add support for recolored images

                if (null !== $imageSrc) {
                    $objects[] = new DocumentObject(
                        $id,
                        DocumentObject::TYPE_IMAGE, [
                            'src' => $imageSrc,
                            'title' => $embeddedObject->getTitle(),
                            'description' => $embeddedObject->getDescription(),
                        ]
                    );
                }
            }

            // @todo add support for drawings
        }

        return $objects;
    }
}
