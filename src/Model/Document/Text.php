<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Model\Document;

class Text implements DocumentElement
{
    // @todo add support for strikethrough, smallCaps

    public function __construct(
        public string $value,
        public bool $bold = false,
        public bool $italic = false,
        public bool $underline = false,
        public ?string $link = null
    ) {
    }

    public function toString(): string
    {
        return $this->value;
    }
}
