<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Model\Document;

final class Paragraph implements DocumentElement
{
    /**
     * @param Text[] $texts
     */
    public function __construct(public array $texts)
    {
    }

    public function toString(): string
    {
        return array_reduce(
            $this->texts,
            static fn (string $carry, Text $text) => $carry.$text->toString(),
            ''
        );
    }
}
