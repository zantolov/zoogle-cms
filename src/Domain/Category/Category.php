<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Domain\Category;

use Zantolov\ZoogleCms\Domain\ValueObject\CategoryId;

class Category
{
    public CategoryId $id;
    public string $slug;
    public ?Category $parent;

    public function __construct(CategoryId $id, string $slug, ?Category $parent)
    {
        $this->id = $id;
        $this->slug = $slug;
        $this->parent = $parent;
    }
}
