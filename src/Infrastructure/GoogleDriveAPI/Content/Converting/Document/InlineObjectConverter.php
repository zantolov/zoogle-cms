<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Content\Converting\Document;

use Zantolov\ZoogleCms\Domain\Document\InlineObject;

/**
 * @internal
 */
class InlineObjectConverter extends AbstractContentElementConverter
{
    /** @return InlineObject[] */
    public function convert(\Google_Service_Docs_Paragraph $paragraph): array
    {
        $inlineObjects = [];
        /** @var \Google_Service_Docs_ParagraphElement $element */
        foreach ($paragraph->getElements() as $element) {
            if ($object = $element->getInlineObjectElement()) {
                $inlineObjects[] = new InlineObject($object->getInlineObjectId());
            }
        }

        return $inlineObjects;
    }

    public function supports(\Google_Service_Docs_Paragraph $paragraph): bool
    {
        /** @var \Google_Service_Docs_ParagraphElement $element */
        foreach ($paragraph->getElements() as $element) {
            if ($element->getInlineObjectElement()) {
                return true;
            }
        }

        return false;
    }
}
