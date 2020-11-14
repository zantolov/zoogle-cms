<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Domain\Post;

final class PostId
{
    public string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
