<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDrive\Content\Processing;

interface ProcessorInterface
{
    public function process(string $html): string;
}
