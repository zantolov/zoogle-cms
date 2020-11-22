<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Application\FindPosts;

use Zantolov\ZoogleCms\Domain\Post\Post;

interface FindPost
{
    public function findBySlug(string $slug): ?Post;

    public function findById(string $id): ?Post;
}
