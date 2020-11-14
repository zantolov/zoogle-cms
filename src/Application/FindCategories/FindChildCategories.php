<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Application\FindCategories;

use Zantolov\ZoogleCms\Domain\Category\Category;

interface FindChildCategories
{
    public function findChildCategories(Category $category): array;
}
