<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Factory;

use Google_Service_Drive_DriveFile;
use Zantolov\ZoogleCms\Domain\Category\Category;
use Zantolov\ZoogleCms\Domain\Category\CategoryId;

class CategoryFactory
{
    public function fromGoogleDriveFile(Google_Service_Drive_DriveFile $file, ?string $parentId = null): Category
    {
        return new Category(
            new CategoryId($file->getId()),
            $file->getName(),
            null !== $parentId ? new CategoryId($parentId) : null
        );
    }
}