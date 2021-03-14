<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Domain\Document;

use Cocur\Chain\Chain;

class Document
{
    /**
     * @param DocumentElement[] $elements
     * @param DocumentList[] $lists
     * @param InlineObject[] $objects
     */
    public function __construct(public string $id, public array $elements, public array $lists, public array $objects)
    {
    }

    public function getList(string $listId): ?DocumentList
    {
        return Chain::create($this->lists)
            ->filter(fn (DocumentList $list) => $list->id === $listId)
            ->first() ?: null;
    }

    public function getObject(string $objectId): ?DocumentObject
    {
        return Chain::create($this->objects)
            ->filter(fn (DocumentObject $object) => $object->id === $objectId)
            ->first() ?: null;
    }

    public function getTitle(): ?Title
    {
        return Chain::create($this->elements)
            ->filter(fn (DocumentElement $element) => $element instanceof Title)
            ->first() ?: null;
    }

    public function getSubtitle(): ?Subtitle
    {
        return Chain::create($this->elements)
            ->filter(fn (DocumentElement $element) => $element instanceof Subtitle)
            ->first() ?: null;
    }

    public function getMetadata(): ?Metadata
    {
        return Chain::create($this->elements)
            ->filter(fn (DocumentElement $element) => $element instanceof Metadata)
            ->first() ?: null;
    }

    /**
     * @return Image[]
     */
    public function getImages(): array
    {
        return Chain::create($this->elements)
            ->filter(fn (DocumentElement $element) => $element instanceof Image)
            ->values()
            ->array;
    }

    public function firstImage(): ?Image
    {
        return $this->getImages()[0] ?? null;
    }

    public function withElements(array $elements): self
    {
        $instance = clone($this);
        $instance->elements = $elements;

        return $instance;
    }

    public function withoutFirstImage(): self
    {
        $items = Chain::create($this->elements)
            ->filter(fn (DocumentElement $element) => $element !== $this->firstImage())
            ->values()
            ->array;

        return $this->withElements($items);
    }
}
