<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Domain\ValueObject;

interface CategoryId
{
    public function toString(): string;
}
