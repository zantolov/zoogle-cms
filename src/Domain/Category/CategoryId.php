<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Domain\Category;

final class CategoryId
{
    public string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
