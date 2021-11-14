<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Bridge\Symfony;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class ZoogleCmsBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new ZoogleCmsExtension();
    }
}
