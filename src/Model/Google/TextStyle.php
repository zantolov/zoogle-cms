<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Model\Google;

use Google\Service\Docs\TextStyle as GoogleTextStyle;

/** @psalm-immutable */
final class TextStyle
{
    public function __construct(private GoogleTextStyle $decorated)
    {
    }

    public function getBold(): bool
    {
        return $this->decorated->getBold() ?: false;
    }

    public function getItalic(): bool
    {
        return $this->decorated->getItalic() ?: false;
    }

    public function getUnderline(): bool
    {
        return $this->decorated->getUnderline() ?: false;
    }

    public function getLinkUrl(): ?string
    {
        /** @phpstan-ignore-next-line */
        return $this->decorated->getLink()?->getUrl();
    }
}
