<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Model;

use Zantolov\ZoogleCms\Domain\ValueObject\CategoryId as CategoryIdInterface;

final class CategoryId implements CategoryIdInterface
{
    /** @var string  */
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function toString(): string
    {
        return $this->id;
    }
}
