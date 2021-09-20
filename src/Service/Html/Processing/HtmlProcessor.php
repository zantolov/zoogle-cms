<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Service\Html\Processing;

interface HtmlProcessor
{
    public function process(string $html): string;
}
