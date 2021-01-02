<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Domain\Post;

use Zantolov\ZoogleCms\Domain\Category\Category;

class Post
{
    public function __construct(
        public PostId $id,
        public string $title,
        public string $slug,
        public string $content,
        public ?\DateTimeImmutable $publishingDateTime,
        public ?string $leadingImageUrl,
        public Category $category,
        public ?Author $author
    ) {
    }

    public function isPublished(\DateTimeInterface $now): bool
    {
        return $this->publishingDateTime !== null && $now >= $this->publishingDateTime;
    }
}
