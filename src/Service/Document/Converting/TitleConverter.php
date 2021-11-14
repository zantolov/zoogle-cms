<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Service\Document\Converting;

use Zantolov\ZoogleCms\Model\Document\Title;
use Zantolov\ZoogleCms\Model\Google\Paragraph;

/**
 * @internal
 */
final class TitleConverter extends AbstractContentElementConverter
{
    /**
     * @return list<Title>
     */
    public function convert(Paragraph $paragraph): array
    {
        $content = $this->getUnformattedParagraphContent($paragraph);

        return [new Title($content)];
    }

    public function supports(Paragraph $paragraph): bool
    {
        return $paragraph->getNamedStyleType() === 'TITLE';
    }
}
