<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Service\Document\Converting;

use Google\Service\Docs\InlineObjectElement;
use Google\Service\Docs\Paragraph;
use Zantolov\ZoogleCms\Model\Document\InlineObject;

/**
 * @internal
 */
final class InlineObjectConverter extends AbstractContentElementConverter
{
    /**
     * @param Paragraph<Paragraph> $paragraph
     *
     * @return list<InlineObject>
     */
    public function convert(Paragraph $paragraph): array
    {
        $inlineObjects = [];
        foreach ($paragraph->getElements() as $element) {
            /** @var ?InlineObjectElement<InlineObjectElement> $object */
            $object = $element->getInlineObjectElement();
            if ($object !== null) {
                $inlineObjects[] = new InlineObject($object->getInlineObjectId());
            }
        }

        return $inlineObjects;
    }

    /**
     * @param Paragraph<Paragraph> $paragraph
     */
    public function supports(Paragraph $paragraph): bool
    {
        foreach ($paragraph->getElements() as $element) {
            /** @var ?InlineObjectElement<InlineObjectElement> $object */
            $object = $element->getInlineObjectElement();
            if ($object !== null) {
                return true;
            }
        }

        return false;
    }
}
