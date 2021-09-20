<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Model\Document;

final class DocumentObject implements DocumentElement
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