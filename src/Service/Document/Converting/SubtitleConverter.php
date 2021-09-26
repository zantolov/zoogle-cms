<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Service\Document\Converting;

use Google\Service\Docs\Paragraph;
use Zantolov\ZoogleCms\Model\Document\Subtitle;

/**
 * @internal
 */
final class SubtitleConverter extends AbstractContentElementConverter
{
    /**
     * @param Paragraph<Paragraph> $paragraph
     *
     * @return list<Subtitle>
     */
    public function convert(Paragraph $paragraph): array
    {
        $content = $this->getUnformattedParagraphContent($paragraph);

        return [new Subtitle($content)];
    }

    /**
     * @param Paragraph<Paragraph> $paragraph
     */
    public function supports(Paragraph $paragraph): bool
    {
        return $paragraph->getParagraphStyle()->getNamedStyleType() === 'SUBTITLE';
    }
}
