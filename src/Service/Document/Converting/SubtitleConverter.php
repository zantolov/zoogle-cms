<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Service\Document\Converting;

use Zantolov\ZoogleCms\Model\Document\Subtitle;
use Zantolov\ZoogleCms\Model\Google\Paragraph;

/**
 * @internal
 */
final class SubtitleConverter extends AbstractContentElementConverter
{
    /**
     * @return list<Subtitle>
     */
    public function convert(Paragraph $paragraph): array
    {
        $content = $this->getUnformattedParagraphContent($paragraph);

        return [new Subtitle($content)];
    }

    public function supports(Paragraph $paragraph): bool
    {
        return $paragraph->getNamedStyleType() === 'SUBTITLE';
    }
}
