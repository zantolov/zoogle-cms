<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Service\Document\Processing;

use Zantolov\ZoogleCms\Model\Document\Document;
use Zantolov\ZoogleCms\Model\Document\DocumentObject;
use Zantolov\ZoogleCms\Model\Document\Image;
use Zantolov\ZoogleCms\Model\Document\InlineObject;

final class ObjectNormalizationProcessor implements DocumentProcessor
{
    public function process(Document $document): Document
    {
        $elements = [];
        foreach ($document->elements as $element) {
            if ($element instanceof InlineObject) {
                $object = $document->getObject($element->id);
                if ($object->type === DocumentObject::TYPE_IMAGE) {
                    $element = new Image(
                        $object->id,
                        $object->properties['src'] ?? null,
                        $object->properties['title'] ?? null,
                        $object->properties['description'] ?? null,
                    );
                }
            }

            $elements[] = $element;
        }

        return $document->withElements($elements);
    }

    private function convertInlineObjectToImage(\Google_Service_Docs_Document $document, InlineObject $object): ?Image
    {
        $objects = $document->getInlineObjects();
        /** @var \Google_Service_Docs_InlineObject $documentObject */
        foreach ($objects as $id => $documentObject) {
            if ($id === $object->id && $documentObject->getInlineObjectProperties()?->getEmbeddedObject()) {
                $embeddedObject = $documentObject->getInlineObjectProperties()?->getEmbeddedObject();

                $imageSrc = $embeddedObject?->getImageProperties()?->getContentUri();
                $alt = $embeddedObject->getTitle();
                $description = $embeddedObject->getDescription();

                // @todo add support for cropped content
                // @todo add support for drawings

                if ($imageSrc !== null) {
                    return new Image($documentObject->getObjectId(), $imageSrc, $alt, $description);
                }
            }
        }

        return null;
    }

    public function priority(): int
    {
        return 0;
    }
}