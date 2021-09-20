<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Model\Document;

class DocumentList implements DocumentElement
{
    public const TYPE_ORDERED = 'ordered';
    public const TYPE_UNORDERED = 'unordered';

    public function __construct(public string $id, public array $items, public string $type)
    {
    }

    public function add(ListItem $item): void
    {
        $this->items[] = $item;
    }

    public function toString(): string
    {
        return array_reduce(
            $this->texts,
            fn (string $carry, ListItem $item) => $carry.$item->toString(),
            ''
        );
    }
}
