<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Domain\Post;

class Author
{
    public string $caption;

    public function __construct(string $caption)
    {
        $this->caption = $caption;
    }

    public function equals(self $author): bool
    {
        return $author->caption === $this->caption;
    }
}
