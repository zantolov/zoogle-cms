<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Content\Processing;

interface ProcessorInterface
{
    public function process(string $html): string;
}
