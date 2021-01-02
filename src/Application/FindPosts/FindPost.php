<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Application\FindPosts;

use Zantolov\ZoogleCms\Domain\Post\Post;
use Zantolov\ZoogleCms\Domain\Post\PostId;

interface FindPost
{
    /**
     * @return Post[]
     */
    public function all(): array;

    public function findBySlug(string $slug): ?Post;

    public function findById(PostId $id): ?Post;
}
