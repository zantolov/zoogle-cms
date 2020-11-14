<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Application\FindPosts;

interface FindUncategorizedPosts
{
    public function find(): array;
}
