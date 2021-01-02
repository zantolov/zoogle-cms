<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Domain\Document;

class Image implements DocumentElement
{
    public function __construct(public string $src)
    {
    }
}
