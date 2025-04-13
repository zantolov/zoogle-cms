<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Content\Html\Processing;

interface HtmlProcessor
{
    public function process(string $html): string;
}
