<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Domain\ValueObject;

interface ArticleId
{
    public function toString(): string;
}
