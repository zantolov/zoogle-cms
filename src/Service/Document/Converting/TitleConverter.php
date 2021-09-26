<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Service\Document\Converting;

use Google\Service\Docs\Paragraph;
use Zantolov\ZoogleCms\Model\Document\Title;

/**
 * @internal
 */
final class TitleConverter extends AbstractContentElementConverter
{
    /**
     * @param Paragraph<Paragraph> $paragraph
     *
     * @return list<Title>
     */
    public function convert(Paragraph $paragraph): array
    {
        $content = $this->getUnformattedParagraphContent($paragraph);

        return [new Title($content)];
    }

    /**
     * @param Paragraph<Paragraph> $paragraph
     */
    public function supports(Paragraph $paragraph): bool
    {
        return $paragraph->getParagraphStyle()->getNamedStyleType() === 'TITLE';
    }
}
