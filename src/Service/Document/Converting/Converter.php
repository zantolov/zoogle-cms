<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Service\Document\Converting;

use Assert\Assertion;
use Google\Service\Docs\Document as GoogleDocument;
use Google\Service\Docs\SectionBreak;
use Google\Service\Docs\StructuralElement;
use Google\Service\Docs\Table;
use Zantolov\ZoogleCms\Model\Document\Document;
use Zantolov\ZoogleCms\Model\Document\DocumentElement;
use Zantolov\ZoogleCms\Model\Document\DocumentList;
use Zantolov\ZoogleCms\Model\Document\DocumentObject;
use Zantolov\ZoogleCms\Model\Document\Metadata;
use Zantolov\ZoogleCms\Model\Google\Paragraph;

final class Converter
{
    /**
     * @param iterable<ElementConverter> $converters
     */
    public function __construct(
        private iterable $converters
    ) {
    }

    public function convert(GoogleDocument $document): Document
    {
        $elements = [];
        $elements[] = $this->generateMetadata($document);
        $lists = $this->generateDocumentLists($document);
        $objects = $this->generateDocumentObjects($document);

        /** @var StructuralElement $element */
        foreach ($document->getBody() as $element) {
            $elements = [...$elements, ...$this->generateElements($element)];
        }

        return new Document($document->getDocumentId(), $elements, $lists, $objects);
    }

    /**
     * @return list<DocumentElement>
     */
    private function generateElements(StructuralElement $element): array
    {
        $elements = [];
        if ($element->getParagraph() !== null) {
            $paragraph = new Paragraph($element->getParagraph());
            foreach ($this->converters as $converter) {
                if ($converter->supports($paragraph)) {
                    $elements = [...$elements, ...$converter->convert($paragraph)];
                }
            }
        }

        /** @var ?Table<Table> $table */
        $table = $element->getTable();
        if ($table !== null) {
            // @todo
        }

        /** @var ?SectionBreak<SectionBreak> $table */
        $break = $element->getSectionBreak();
        if ($break !== null) {
            // @todo
        }

        return $elements;
    }

    private function generateMetadata(GoogleDocument $doc): Metadata
    {
        $headers = $doc->getHeaders();
        $header = array_values($headers)[0] ?? null;
        if ($header === null) {
            return new Metadata([]);
        }

        $meta = [];
        $items = $header->getContent();
        $items = array_map(
            fn (\Google_Service_Docs_StructuralElement $element) => array_reduce(
                $this->generateElements($element),
                static fn (string $carry, DocumentElement $element) => $carry.$element->toString(),
                ''
            ),
            $items
        );

        foreach ($items as $item) {
            $components = \Safe\preg_split('/[\n\v]+/', $item);
            $components = array_filter($components);
            $keyValues = array_map(
                static fn (string $line) => explode(':', $line, 2),
                $components
            );
            foreach ($keyValues as $keyValue) {
                $key = $keyValue[0] ?? null;
                Assertion::string($key);
                $key = trim($key);

                $value = $keyValue[1] ?? null;
                Assertion::nullOrString($value);
                $value = $value === null ? null : trim($value);

                $meta[mb_strtolower($key)] = $value;
            }
        }

        return new Metadata($meta);
    }

    /**
     * @return list<DocumentList>
     */
    private function generateDocumentLists(GoogleDocument $document): array
    {
        $lists = [];
        foreach ($document->getLists() as $listId => $list) {
            $listProperties = $list->getListProperties();
            $nestingLevels = $listProperties->getNestingLevels() ?? [];
            $firstNestingLevel = $nestingLevels[0] ?? null;

            $listType = match ($firstNestingLevel?->getGlyphType()) {
                'DECIMAL' => DocumentList::TYPE_ORDERED,
                default => DocumentList::TYPE_UNORDERED
            };

            $lists[] = new DocumentList($listId, [], $listType);
        }

        return $lists;
    }

    /**
     * @return list<DocumentObject>
     */
    private function generateDocumentObjects(GoogleDocument $document): array
    {
        $objects = [];
        foreach ($document->getInlineObjects() as $id => $documentObject) {
            $properties = $documentObject->getInlineObjectProperties();
            $embeddedObject = $properties->getEmbeddedObject();

            $imageSrc = $embeddedObject->getImageProperties()->getContentUri();

            // @todo add support for cropped images
            // @todo add support for recolored images

            if ($imageSrc !== null) {
                $objects[] = new DocumentObject(
                    $id,
                    DocumentObject::TYPE_IMAGE,
                    [
                        'src' => $imageSrc,
                        'title' => $embeddedObject->getTitle(),
                        'description' => $embeddedObject->getDescription(),
                    ]
                );
            }

            // @todo add support for drawings
        }

        return $objects;
    }
}
