<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Model\Document;

class Heading implements DocumentElement
{
    public function __construct(public string $value, public int $level)
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
