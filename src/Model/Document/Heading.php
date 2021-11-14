<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Model\Document;

final class Heading implements \Stringable, DocumentElement
{
    public function __construct(public string $value, public int $level)
    {
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
