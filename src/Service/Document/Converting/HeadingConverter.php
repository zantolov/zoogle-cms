<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Service\Document\Converting;

use Zantolov\ZoogleCms\Model\Document\Heading;
use Zantolov\ZoogleCms\Model\Google\Paragraph;

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
     * @return list<Heading>
     */
    public function convert(Paragraph $paragraph): array
    {
        $content = $this->getUnformattedParagraphContent($paragraph);
        $level = static::$headings[$paragraph->getNamedStyleType()] ?? null;

        if ($level === null) {
            throw new \RuntimeException('Unsupported heading given');
        }

        return [new Heading($content, $level)];
    }

    public function supports(Paragraph $paragraph): bool
    {
        $style = $paragraph->getNamedStyleType();
        if ($style === null) {
            return false;
        }

        return \array_key_exists($style, static::$headings);
    }
}
