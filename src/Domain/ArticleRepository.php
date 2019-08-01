<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Domain;

use Zantolov\ZoogleCms\Domain\ValueObject\ArticleId;
use Zantolov\ZoogleCms\Domain\ValueObject\CategoryId;

interface ArticleRepository
{
    public function get(ArticleId $articleId): Article;

    /** @return Article[] */
    public function findAllInCategory(CategoryId $categoryId): array;

    /** @return Article|null */
    public function findBySlugAndCategory(string $slug, CategoryId $categoryId): ?Article;

    public function findBySlug(string $slug): ?Article;
}
