<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Domain;

use Zantolov\ZoogleCms\Domain\ValueObject\CategoryId;

interface Category
{
    public function getId(): CategoryId;

    public function getSlug(): string;

    public function getParentId(): ?CategoryId;
}
