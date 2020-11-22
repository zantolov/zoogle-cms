<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Application\FindPosts;

use Zantolov\ZoogleCms\Domain\Category\CategoryId;
use Zantolov\ZoogleCms\Domain\Post\Post;

interface FindPostsInCategory
{
    public function allInCategory(CategoryId $id): array;

    public function findInCategoryBySlug(CategoryId $id, string $slug): ?Post;
}
