<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Domain\Category;

final class CategoryId
{
    public function __construct(public string $value)
    {
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
