<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Service\Document\Converting;


use Zantolov\ZoogleCms\Model\Document\Heading;

/**
 * @internal
 */
class HeadingConverter extends AbstractContentElementConverter
{
    private static $headings = [
        'HEADING_1' => 1,
        'HEADING_2' => 2,
        'HEADING_3' => 3,
        'HEADING_4' => 4,
        'HEADING_5' => 5,
        'HEADING_6' => 6,
    ];

    /** @return Heading[] */
    public function convert(\Google_Service_Docs_Paragraph $paragraph): array
    {
        $content = $this->getUnformattedParagraphContent($paragraph);
        $level = static::$headings[$paragraph->getParagraphStyle()?->getNamedStyleType()];

        return [new Heading($content, $level)];
    }

    public function supports(\Google_Service_Docs_Paragraph $paragraph): bool
    {
        return array_key_exists($paragraph->getParagraphStyle()?->getNamedStyleType(), static::$headings);
    }
}
