<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDrive\Content\Converting\Document;


use Zantolov\ZoogleCms\Domain\Document\Title;

/**
 * @internal
 */
class TitleConverter extends AbstractContentElementConverter
{
    /** @return Title[] */
    public function convert(\Google_Service_Docs_Paragraph $paragraph): array
    {
        $content = $this->getUnformattedParagraphContent($paragraph);

        return [new Title($content)];
    }

    public function supports(\Google_Service_Docs_Paragraph $paragraph): bool
    {
        return 'TITLE' === $paragraph->getParagraphStyle()?->getNamedStyleType();
    }
}
