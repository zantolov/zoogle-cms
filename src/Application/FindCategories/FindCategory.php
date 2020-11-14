<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Application\FindCategories;

use Zantolov\ZoogleCms\Domain\Category\Category;

interface FindCategory
{
    public function find(string $slug): ?Category;
}
