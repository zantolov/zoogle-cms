<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Service\Document\Converting;

use Zantolov\ZoogleCms\Model\Document\InlineObject;
use Zantolov\ZoogleCms\Model\Google\Paragraph;
use Zantolov\ZoogleCms\Model\Google\ParagraphElement;

/**
 * @internal
 */
final class InlineObjectConverter extends AbstractContentElementConverter
{
    /**
     * @return list<InlineObject>
     */
    public function convert(Paragraph $paragraph): array
    {
        $inlineObjects = [];
        /** @var ParagraphElement $element */
        foreach ($paragraph->getElements() as $element) {
            $object = $element->getInlineObjectElement();
            if ($object !== null) {
                $inlineObjects[] = new InlineObject($object->getInlineObjectId());
            }
        }

        return $inlineObjects;
    }

    public function supports(Paragraph $paragraph): bool
    {
        foreach ($paragraph->getElements() as $element) {
            if ($element->getInlineObjectElement() !== null) {
                return true;
            }
        }

        return false;
    }
}
