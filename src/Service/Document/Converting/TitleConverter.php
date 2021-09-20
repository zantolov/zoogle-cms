<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Service\Document\Converting;

use Zantolov\ZoogleCms\Model\Document\Title;

/**
 * @internal
 */
final class TitleConverter extends AbstractContentElementConverter
{
    /** @return Title[] */
    public function convert(\Google_Service_Docs_Paragraph $paragraph): array
    {
        $content = $this->getUnformattedParagraphContent($paragraph);

        return [new Title($content)];
    }

    public function supports(\Google_Service_Docs_Paragraph $paragraph): bool
    {
        return $paragraph->getParagraphStyle()?->getNamedStyleType() === 'TITLE';
    }
}
