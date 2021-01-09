<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDrive\Content\Html\Processing;

use Zantolov\ZoogleCms\Domain\Document\ContentElement;

interface HtmlProcessor
{
    public function process(string $html): string;
}
