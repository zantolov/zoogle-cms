<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDrive\Content\Document\Processing;

use Zantolov\ZoogleCms\Domain\Document\Document;
use Zantolov\ZoogleCms\Domain\Document\DocumentObject;
use Zantolov\ZoogleCms\Domain\Document\Image;
use Zantolov\ZoogleCms\Domain\Document\InlineObject;

class ObjectNormalizationPass implements ProcessingPass
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
        /** @var  \Google_Service_Docs_InlineObject $documentObject */
        foreach ($objects as $id => $documentObject) {
            if ($id === $object->id && $documentObject->getInlineObjectProperties()?->getEmbeddedObject()) {
                $embeddedObject = $documentObject->getInlineObjectProperties()?->getEmbeddedObject();

                $imageSrc = $embeddedObject?->getImageProperties()?->getContentUri();
                $alt = $embeddedObject->getTitle();
                $description = $embeddedObject->getDescription();

                // @todo add support for cropped content
                // @todo add support for drawings

                if (null !== $imageSrc) {
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
