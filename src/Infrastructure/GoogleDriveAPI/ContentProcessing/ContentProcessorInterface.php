<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\ContentProcessing;

interface ContentProcessorInterface
{
    public function process(string $html): string;
}
