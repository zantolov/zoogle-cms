<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Configuration;

final class Configuration
{
    /** @var string  */
    private $rootDirectoryId;

    public function __construct(string $rootDirectoryId)
    {
        $this->rootDirectoryId = $rootDirectoryId;
    }

    public function getRootDirectoryId(): string
    {
        return $this->rootDirectoryId;
    }
}
