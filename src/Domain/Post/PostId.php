<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Domain\Post;

final class PostId
{
    public string $value;

    public function __construct(string $id)
    {
        $this->value = $id;
    }
}
