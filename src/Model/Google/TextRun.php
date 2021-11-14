<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Model\Google;

use Google\Service\Docs\TextRun as GoogleTextRun;

/** @psalm-immutable */
final class TextRun
{
    /** @phpstan-ignore-next-line */
    public function __construct(private GoogleTextRun $decorated)
    {
    }

    public function getContent(): ?string
    {
        return $this->decorated->getContent();
    }

    public function getTextStyle(): ?TextStyle
    {
        /** @phpstan-ignore-next-line */
        return $this->decorated->getTextStyle() ? new TextStyle($this->decorated->getTextStyle()) : null;
    }
}
