<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Service\Document\Converting;

use Google\Service\Docs\Paragraph;
use Zantolov\ZoogleCms\Model\Document\DocumentElement;

/**
 * @internal
 */
interface ElementConverter
{
    /**
     * @param Paragraph<Paragraph> $paragraph
     *
     * @return list<DocumentElement>
     */
    public function convert(Paragraph $paragraph): array;

    /**
     * @param Paragraph<Paragraph> $paragraph
     */
    public function supports(Paragraph $paragraph): bool;
}
