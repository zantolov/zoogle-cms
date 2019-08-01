<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Model;

use DateTimeInterface;
use Zantolov\ZoogleCms\Domain\Article as ArticleInterface;
use Zantolov\ZoogleCms\Domain\ValueObject\ArticleId;

final class Article implements ArticleInterface
{
    /** @var ArticleId */
    private $id;

    /** @var string */
    private $title;

    /** @var string */
    private $slug;

    /** @var string */
    private $content;

    /** @var DateTimeInterface|null */
    private $publishDateTime;

    /** @var string|null */
    private $authorCaption;

    /** @var string|null */
    private $headingImageUrl;

    /** @var array */
    private $tags;

    public function __construct(
        ArticleId $id,
        string $title,
        string $slug,
        string $content,
        ?DateTimeInterface $publishDateTime = null,
        ?string $authorCaption = null,
        ?string $headingImageUrl = null,
        array $tags = []
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->slug = $slug;
        $this->publishDateTime = $publishDateTime;
        $this->content = $content;
        $this->authorCaption = $authorCaption;
        $this->headingImageUrl = $headingImageUrl;
        $this->tags = $tags;
    }

    public function getId(): ArticleId
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getAuthorCaption(): ?string
    {
        return $this->authorCaption;
    }

    public function getHeadingImageUrl(): ?string
    {
        return $this->headingImageUrl;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function getPublishDateTime(): ?DateTimeInterface
    {
        return $this->publishDateTime;
    }
}
