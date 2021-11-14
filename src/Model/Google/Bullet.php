<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Model\Google;

use Google\Service\Docs\Bullet as GoogleBullet;

/** @psalm-immutable */
final class Bullet
{
    public function __construct(private GoogleBullet $decorated)
    {
    }

    public function getListId(): ?string
    {
        return $this->decorated->getListId();
    }

    public function getNestingLevel(): ?int
    {
        return $this->decorated->getNestingLevel();
    }
}
