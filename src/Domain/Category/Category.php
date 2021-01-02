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
        return $this->hasSlug($category->slug);
    }

    public function hasSlug(string $slug): bool
    {
        return $slug === $this->slug;
    }
}
