<?php

declare(strict_types=1);

namespace Zantolov\Zoogle\Cms\Service\Html\Processing;

interface HtmlProcessor
{
    public function process(string $html): string;
}
