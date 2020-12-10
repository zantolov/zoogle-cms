<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Domain\Post;

final class PostId
{
    public function __construct(public string $value)
    {
    }
}
