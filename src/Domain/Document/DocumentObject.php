<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Domain\Document;

class DocumentObject implements DocumentElement
{
    public const TYPE_IMAGE = 'image';

    public function __construct(public string $id, public string $type, public array $properties)
    {
    }

    public function toString(): string
    {
        return $this->id;
    }
}
