<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Domain\Document;

use Cocur\Chain\Chain;

class Document
{
    public function __construct(public array $items)
    {
    }

    public function getTitle(): ?Title
    {
        return Chain::create($this->items)
                ->filter(fn (DocumentElement $element) => $element instanceof Title)
                ->first() ?? null;
    }

    public function getMetadata(): ?Metadata
    {
        return Chain::create($this->items)
                ->filter(fn (DocumentElement $element) => $element instanceof Metadata)
                ->first() ?? null;
    }

    /**
     * @return Image[]
     */
    public function getImages(): array
    {
        return Chain::create($this->items)
            ->filter(fn (DocumentElement $element) => $element instanceof Image)
            ->values()
            ->array;
    }

    public function firstImage(): ?Image
    {
        return $this->getImages()[0] ?? null;
    }
}
