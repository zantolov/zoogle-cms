<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Content\Converting\Document;

use Zantolov\ZoogleCms\Domain\Document\DocumentList;

class ListProcessor
{
    public function initializeList(\Google_Service_Docs_Document $document, string $listId): DocumentList
    {
        $listType = $this->getListType($document, $listId);
        $list = new DocumentList($listId, [], $listType);

        return $list;
    }

    private function getListType(\Google_Service_Docs_Document $doc, string $listId): string
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

}
