<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Model\Google;

use Google\Service\Docs\InlineObjectElement;
use Google\Service\Docs\ParagraphElement as GoogleParagraphElement;

/** @psalm-immutable */
final class ParagraphElement
{
    public function __construct(private GoogleParagraphElement $decorated)
    {
    }

    public function getTextRun(): ?TextRun
    {
        /** @phpstan-ignore-next-line */
        return $this->decorated->getTextRun() ? new TextRun($this->decorated->getTextRun()) : null;
    }

    /** @phpstan-ignore-next-line */
    public function getInlineObjectElement(): ?InlineObjectElement
    {
        return $this->decorated->getInlineObjectElement();
    }
}
