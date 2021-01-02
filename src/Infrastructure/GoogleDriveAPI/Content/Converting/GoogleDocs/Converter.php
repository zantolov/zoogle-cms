<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Content\Converting\GoogleDocs;

use Google_Service_Docs_Document;
use Zantolov\ZoogleCms\Domain\Document\Document;
use Zantolov\ZoogleCms\Domain\Document\DocumentElement;
use Zantolov\ZoogleCms\Domain\Document\DocumentList;
use Zantolov\ZoogleCms\Domain\Document\ListItem;
use Zantolov\ZoogleCms\Domain\Document\Metadata;
use Zantolov\ZoogleCms\Domain\Document\Paragraph;

/**
 * @internal
 */
class Converter
{
    /** @var ElementConverter[] $converters */
    private iterable $converters = [];

    /**
     * @param ElementConverter[] $converters
     */
    public function __construct(iterable $converters)
    {
        $this->converters = $converters;
    }

    public function convert(Google_Service_Docs_Document $doc): Document
    {
        $elements = [];

        /** @var \Google_Service_Docs_StructuralElement $element */
        foreach ($doc->getBody() as $element) {
            $elements = [...$elements, ...$this->generateElements($element)];
        }

        $processedElements = [];
        $lists = [];
        foreach ($elements as $element) {
            if ($element instanceof ListItem) {
                $list = $lists[$element->listId] ?? null;
                if (null === $list) {
                    $listType = $this->getListType($doc, $element->listId);
                    $list = new DocumentList($element->listId, [], $listType);
                    $lists[$list->id] = $list;
                    $processedElements[] = $list;
                }
                $list->add($element);
                continue;
            }

            $processedElements[] = $element;
        }

        $processedElements[] = $this->processMetadata($doc);

        // @todo add list definitions to the document

        return new Document($processedElements);
    }

    private function generateElements(\Google_Service_Docs_StructuralElement $element): array
    {
        if ($paragraph = $element->getParagraph()) {
            foreach ($this->converters as $converter) {
                if ($converter->supports($paragraph)) {
                    return $converter->convert($paragraph);
                }
            }
        }

        if ($element->getTable()) {
            // @todo
        }

        if ($element->getSectionBreak()) {
            // @todo
        }

//        dump($element);

        return [];
    }

    private function getListType(Google_Service_Docs_Document $doc, string $listId): string
    {
        $firstNestingLevel = null;

        foreach ($doc->getLists() as $id => $list) {
            if ($id === $listId) {
                $listProperties = $list->getListProperties();
                $nestingLevels = $listProperties?->getNestingLevels() ?? [];
                $firstNestingLevel = $nestingLevels[0] ?? null;
            }
        }

        return match ($firstNestingLevel?->getGlyphType()) {
            'DECIMAL' => DocumentList::TYPE_ORDERED,
            default => DocumentList::TYPE_UNORDERED
        };
    }

    /**
     * Extract key-value information from the document header.
     */
    private function processMetadata(Google_Service_Docs_Document $doc): Metadata
    {
        $headers = $doc->getHeaders();
        $header = current($headers) ?? null;
        if (null === $header) {
            return new Metadata([]);
        }

        $meta = [];
        try {
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
        } catch (\Throwable $e) {
        }

        return new Metadata($meta);
    }
}
