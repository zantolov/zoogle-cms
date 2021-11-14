<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Service\Document\Converting;

use Zantolov\ZoogleCms\Model\Document\DocumentElement;
use Zantolov\ZoogleCms\Model\Google\Paragraph;

/**
 * @internal
 */
interface ElementConverter
{
    /**
     * @return list<DocumentElement>
     */
    public function convert(Paragraph $paragraph): array;

    public function supports(Paragraph $paragraph): bool;
}
