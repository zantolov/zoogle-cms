<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Domain;

use Zantolov\ZoogleCms\Domain\ValueObject\CategoryId;

interface CategoryRepository
{
    public function get(CategoryId $articleId): Category;

    /** @return Category[] */
    public function findAll(): array;

    public function findBySlug(string $slug): ?Category;
}
