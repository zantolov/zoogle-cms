<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Domain\Category;

class Category
{
    public CategoryId $id;
    public string $slug;
    public ?CategoryId $parentId;

    public function __construct(CategoryId $id, string $slug, ?CategoryId $parentId)
    {
        $this->id = $id;
        $this->slug = $slug;
        $this->parent = $parentId;
    }
}
