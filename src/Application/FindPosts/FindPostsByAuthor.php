<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Application\FindPosts;

interface FindPostsByAuthor
{
    public function findByAuthor(): array;
}
