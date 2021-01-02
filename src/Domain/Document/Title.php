<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Domain\Document;

class Title implements DocumentElement
{
    public function __construct(public string $value)
    {
    }

    public function toString(): string
    {
        return $this->value;
    }
}
