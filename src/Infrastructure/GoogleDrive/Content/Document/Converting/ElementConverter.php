<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDrive\Content\Document\Converting;

use App\Domain\Document\Model\ContentElement;

/**
 * @internal
 */
interface ElementConverter
{
    /** @return ContentElement[] */
    public function convert(\Google_Service_Docs_Paragraph $paragraph): array;

    public function supports(\Google_Service_Docs_Paragraph $paragraph): bool;
}
