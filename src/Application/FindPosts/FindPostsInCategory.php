<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Application\FindPosts;

use Zantolov\ZoogleCms\Domain\Category\CategoryId;

interface FindPostsInCategory
{
    public function find(CategoryId $id): array;
}
