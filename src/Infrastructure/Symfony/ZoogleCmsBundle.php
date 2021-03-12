<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\Symfony;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ZoogleCmsBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new ZoogleCmsExtension();
    }
}
