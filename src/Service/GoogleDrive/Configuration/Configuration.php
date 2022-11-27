<?php

declare(strict_types=1);

namespace Zantolov\Zoogle\Cms\Service\GoogleDrive\Configuration;

final class Configuration
{
    public function __construct(public string $rootDirectoryId)
    {
    }
}
