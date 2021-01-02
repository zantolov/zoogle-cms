<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Content\Converting\Document;

use Google_Service_Docs_Document;
use Zantolov\ZoogleCms\Domain\Document\Document;
use Zantolov\ZoogleCms\Domain\Document\DocumentElement;
use Zantolov\ZoogleCms\Domain\Document\DocumentList;
use Zantolov\ZoogleCms\Domain\Document\Image;
use Zantolov\ZoogleCms\Domain\Document\InlineObject;
use Zantolov\ZoogleCms\Domain\Document\ListItem;
use Zantolov\ZoogleCms\Domain\Document\Metadata;

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

        // Walk over the document elements and finally process the model
        foreach ($elements as $element) {

            // Group all list items in the DocumentList object
            if ($element instanceof ListItem) {
                // Add the list to both $lists and $processedElements only once
                $list = $lists[$element->listId] ??=
                    ($processedElements[] = $this->initializeList($doc, $element->listId));
                $list->add($element);
                continue;
            }

            // Replace object references with corresponding concrete objects
            if ($element instanceof InlineObject) {
                if ($image = $this->convertInlineObjectToImage($doc, $element)) {
                    $processedElements[] = $image;
                }
                continue;
            }

            $processedElements[] = $element;
        }

        $processedElements[] = $this->processMetadata($doc);

        return new Document($processedElements);
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

    private function initializeList(Google_Service_Docs_Document $document, string $listId): DocumentList
    {
        $listType = $this->getListType($document, $listId);
        $list = new DocumentList($listId, [], $listType);

        return $list;
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

    private function convertInlineObjectToImage(Google_Service_Docs_Document $doc, InlineObject $object): ?Image
    {
        $objects = $doc->getInlineObjects();
        /** @var  \Google_Service_Docs_InlineObject $documentObject */
        foreach ($objects as $id => $documentObject) {
            if ($id === $object->id && $documentObject->getInlineObjectProperties()?->getEmbeddedObject()) {
                $embeddedObject = $documentObject->getInlineObjectProperties()?->getEmbeddedObject();

                $imageSrc = $embeddedObject?->getImageProperties()?->getContentUri();
                $alt = $embeddedObject->getTitle();
                $description = $embeddedObject->getDescription();

                // @todo reupload the image to external service as the Content URI is only 30min available
                // @todo add support for cropped content

                if (null !== $imageSrc) {
                    return new Image($imageSrc, $alt, $description);
                }
            }
        }

        return null;
    }

    /**
     * Extract key-value information from the document header.
     */
    private function processMetadata(Google_Service_Docs_Document $doc): Metadata
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
}
