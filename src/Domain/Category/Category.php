<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Domain\Category;

class Category
{
    public function __construct(public CategoryId $id, public string $slug, public ?CategoryId $parentId)
    {
    }

    public function equals(self $category): bool
    {
        return $category->slug === $this->slug;
    }
}
