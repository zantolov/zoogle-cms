<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDrive\Configuration;

final class Configuration
{
    public function __construct(public string $rootDirectoryId)
    {
    }
}
