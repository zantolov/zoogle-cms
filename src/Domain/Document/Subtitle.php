<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Domain\Document;

class Subtitle implements DocumentElement
{
    public function __construct(public string $value)
    {
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function __toString()
    {
        return $this->toString();
    }
}
