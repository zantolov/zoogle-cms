<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDrive\Content\Document\Converting;


use Zantolov\ZoogleCms\Domain\Document\Subtitle;
use Zantolov\ZoogleCms\Domain\Document\Title;

/**
 * @internal
 */
class SubtitleConverter extends AbstractContentElementConverter
{
    /** @return Title[] */
    public function convert(\Google_Service_Docs_Paragraph $paragraph): array
    {
        $content = $this->getUnformattedParagraphContent($paragraph);

        return [new Subtitle($content)];
    }

    public function supports(\Google_Service_Docs_Paragraph $paragraph): bool
    {
        return 'SUBTITLE' === $paragraph->getParagraphStyle()?->getNamedStyleType();
    }
}
