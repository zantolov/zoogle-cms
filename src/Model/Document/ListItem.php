<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Model\Document;

class ListItem implements DocumentElement
{
    /**
     * @param Text[] $texts
     */
    public function __construct(public string $listId, public array $texts, public int $level = 1)
    {
    }

    public function toString(): string
    {
        return array_reduce(
            $this->texts,
            fn (string $carry, Text $text) => $carry.$text->toString(),
            ''
        );
    }
}
