<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Bridge\Symfony;

use Symfony\Component\HttpKernel\Bundle\Bundle;

final class ZoogleCmsBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new ZoogleCmsExtension();
    }
}
