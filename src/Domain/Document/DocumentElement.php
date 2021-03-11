<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Domain\Document;

interface DocumentElement
{
    public function toString(): string;
}
