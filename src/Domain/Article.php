<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Domain;

use DateTimeInterface;
use Zantolov\ZoogleCms\Domain\ValueObject\ArticleId;

interface Article
{
    public function getId(): ArticleId;

    public function getTitle(): string;

    public function getSlug(): string;

    public function getContent(): string;

    public function getPublishDateTime(): ?DateTimeInterface;

    public function getAuthorCaption(): ?string;

    public function getHeadingImageUrl(): ?string;

    public function getTags(): array;
}
