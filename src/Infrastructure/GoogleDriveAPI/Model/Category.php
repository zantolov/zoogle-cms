<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Model;

use Google_Service_Drive_DriveFile;
use Zantolov\ZoogleCms\Domain\Category as CategoryInterface;
use Zantolov\ZoogleCms\Domain\ValueObject\CategoryId as CategoryIdInterface;

final class Category implements CategoryInterface
{
    /** @var CategoryIdInterface */
    private $id;

    /** @var string */
    private $slug;

    /** @var CategoryIdInterface|null */
    private $parentId;

    public static function fromGoogleDriveFile(Google_Service_Drive_DriveFile $file, ?string $parentId = null)
    {
        return new self(
            new CategoryId($file->getId()),
            $file->getName(),
            null !== $parentId ? new CategoryId($parentId) : null
        );
    }

    public function __construct(CategoryIdInterface $id, string $slug, ?CategoryIdInterface $parentId)
    {
        $this->id = $id;
        $this->slug = $slug;
        $this->parentId = $parentId;
    }

    public function getId(): CategoryIdInterface
    {
        return $this->id;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getParentId(): ?CategoryIdInterface
    {
        return $this->parentId;
    }
}
