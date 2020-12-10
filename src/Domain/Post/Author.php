<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Domain\Post;

class Author
{
    public function __construct(public string $caption)
    {
    }

    public function equals(self $author): bool
    {
        return $author->caption === $this->caption;
    }
}
