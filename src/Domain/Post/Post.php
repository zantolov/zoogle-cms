<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Domain\Post;

class Post
{
    public PostId $id;
    public string $title;
    public string $slug;
    public string $content;
    public \DateTimeImmutable $publishDateTime;
    public ?string $headingImageUrl;
    public ?Author $author;

    public function __construct(
        PostId $id,
        string $title,
        string $slug,
        string $content,
        \DateTimeImmutable $publishDateTime,
        ?string $headingImageUrl,
        ?Author $author
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->slug = $slug;
        $this->content = $content;
        $this->publishDateTime = $publishDateTime;
        $this->headingImageUrl = $headingImageUrl;
        $this->author = $author;
    }
}
