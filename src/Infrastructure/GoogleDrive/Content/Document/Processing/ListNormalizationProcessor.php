<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDrive\Content\Document\Processing;

use src\Infrastructure\GoogleDrive\Content\Document\Processing\AbstractProcessingPass;
use Zantolov\ZoogleCms\Domain\Document\Document;
use Zantolov\ZoogleCms\Domain\Document\DocumentList;
use Zantolov\ZoogleCms\Domain\Document\ListItem;

/**
 * Groups all the ListItem objects to the DocumentList object, so that it can be handled as a group.
 */
class ListNormalizationProcessor implements DocumentProcessor
{
    public function process(Document $document): Document
    {
        $lists = [];
        $elements = [];
        foreach ($document->elements as $element) {
            if ($element instanceof ListItem) {
                if (false === isset($lists[$element->listId])) {
                    $list = $document->getList($element->listId);
                    $lists[$element->listId] = $list;
                    $elements[] = $list;
                }
                $list = $lists[$element->listId];
                $list->add($element);
                continue;
            }

            $elements[] = $element;
        }

        return $document->withElements($elements);
    }

    public function priority(): int
    {
        return 0;
    }
}
