<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Service\Document\Converting;

use Google\Service\Docs\Paragraph;
use Zantolov\ZoogleCms\Model\Document\Heading;

/**
 * @internal
 */
final class HeadingConverter extends AbstractContentElementConverter
{
    /**
     * @var array<string, int>
     */
    private static array $headings = [
        'HEADING_1' => 1,
        'HEADING_2' => 2,
        'HEADING_3' => 3,
        'HEADING_4' => 4,
        'HEADING_5' => 5,
        'HEADING_6' => 6,
    ];

    /**
     * @param Paragraph<Paragraph> $paragraph
     *
     * @return list<Heading>
     */
    public function convert(Paragraph $paragraph): array
    {
        $content = $this->getUnformattedParagraphContent($paragraph);
        $level = static::$headings[$paragraph->getParagraphStyle()->getNamedStyleType()];

        return [new Heading($content, $level)];
    }

    /**
     * @param Paragraph<Paragraph> $paragraph
     */
    public function supports(Paragraph $paragraph): bool
    {
        return \array_key_exists($paragraph->getParagraphStyle()->getNamedStyleType(), static::$headings);
    }
}
