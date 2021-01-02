<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Content\Converting\Document;

use Google_Service_Docs_Document;
use Zantolov\ZoogleCms\Domain\Document\Document;
use Zantolov\ZoogleCms\Domain\Document\InlineObject;
use Zantolov\ZoogleCms\Domain\Document\ListItem;

/**
 * @internal
 */
class Converter
{
    /**
     * @param ElementConverter[] $converters
     */
    public function __construct(
        private iterable $converters,
        private ImageProcessor $imageProcessor,
        private MetadataProcessor $metadataProcessor,
        private ListProcessor $listProcessor
    ) {
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
                if (false === isset($lists[$element->listId])) {
                    $list = $this->listProcessor->initializeList($doc, $element->listId);
                    $lists[$element->listId] = $list;
                    $processedElements[] = $list;
                }
                $list = $lists[$element->listId];
                $list->add($element);
                continue;
            }

            // Replace object references with corresponding concrete objects
            if ($element instanceof InlineObject) {
                if ($image = $this->imageProcessor->convertInlineObjectToImage($doc, $element)) {
                    $processedElements[] = $image;
                }
                continue;
            }

            $processedElements[] = $element;
        }

        $processedElements[] = $this->metadataProcessor->processMetadata($doc);

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
}
