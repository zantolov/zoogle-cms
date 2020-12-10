<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Application\FindPosts;

use Zantolov\ZoogleCms\Domain\Post\Post;

interface FindPost
{
    /**
     * @return Post[]
     */
    public function all(): array;

    public function findBySlug(string $slug): ?Post;

    public function findById(string $id): ?Post;
}
