<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Service\GoogleDrive\Configuration;

final class Configuration
{
    public function __construct(public string $rootDirectoryId)
    {
    }
}
