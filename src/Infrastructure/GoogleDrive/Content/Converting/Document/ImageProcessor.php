<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDrive\Content\Converting\Document;

use Zantolov\ZoogleCms\Domain\Document\Image;
use Zantolov\ZoogleCms\Domain\Document\InlineObject;

class ImageProcessor
{
    public function convertInlineObjectToImage(\Google_Service_Docs_Document $doc, InlineObject $object): ?Image
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
}
