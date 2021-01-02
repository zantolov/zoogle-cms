<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Domain\Document;

class InlineObject implements DocumentElement
{
    public function __construct(public string $id)
    {
    }

    public function toString(): string
    {
        return $this->id;
    }
}
