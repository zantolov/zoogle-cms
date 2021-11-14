<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Model\Document;

use Cocur\Chain\Chain;

class Document
{
    /**
     * @param list<DocumentElement> $elements
     * @param list<DocumentList> $lists
     * @param list<DocumentObject> $objects
     */
    public function __construct(public string $id, public array $elements, public array $lists, public array $objects)
    {
    }

    /**
     * @return list<DocumentList>
     */
    public function getLists(): array
    {
        return $this->lists;
    }

    /**
     * @return list<DocumentObject>
     */
    public function getObjects(): array
    {
        return $this->objects;
    }

    public function getList(string $listId): ?DocumentList
    {
        return Chain::create($this->lists)
            ->filter(static fn (DocumentList $list): bool => $list->id === $listId)
            ->first() ?: null;
    }

    public function getObject(string $objectId): ?DocumentObject
    {
        return Chain::create($this->objects)
            ->filter(static fn (DocumentObject $object): bool => $object->id === $objectId)
            ->first() ?: null;
    }

    public function getTitle(): ?Title
    {
        return Chain::create($this->elements)
            ->filter(static fn (DocumentElement $element): bool => $element instanceof Title)
            ->first() ?: null;
    }

    public function getSubtitle(): ?Subtitle
    {
        return Chain::create($this->elements)
            ->filter(static fn (DocumentElement $element): bool => $element instanceof Subtitle)
            ->first() ?: null;
    }

    public function getMetadata(): ?Metadata
    {
        return Chain::create($this->elements)
            ->filter(static fn (DocumentElement $element): bool => $element instanceof Metadata)
            ->first() ?: null;
    }

    /**
     * @return Image[]
     */
    public function getImages(): array
    {
        return Chain::create($this->elements)
            ->filter(static fn (DocumentElement $element): bool => $element instanceof Image)
            ->values()
            ->array;
    }

    /**
     * @return Paragraph[]
     */
    public function getParagraphs(): array
    {
        return Chain::create($this->elements)
            ->filter(static fn (DocumentElement $element): bool => $element instanceof Paragraph)
            ->values()
            ->array;
    }

    public function firstImage(): ?Image
    {
        return $this->getImages()[0] ?? null;
    }

    /**
     * @param list<DocumentElement> $elements
     */
    public function withElements(array $elements): self
    {
        $instance = clone $this;
        $instance->elements = $elements;

        return $instance;
    }

    public function withoutFirstImage(): self
    {
        $items = Chain::create($this->elements)
            ->filter(fn (DocumentElement $element): bool => $element !== $this->firstImage())
            ->values()
            ->array;

        return $this->withElements($items);
    }
}
