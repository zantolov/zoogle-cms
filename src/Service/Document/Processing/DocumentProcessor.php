<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Service\Document\Processing;

use Zantolov\ZoogleCms\Model\Document\Document;

interface DocumentProcessor
{
    public function process(Document $document): Document;

    /**
     * Lower priority gets executed first.
     * Useful for defining dependencies when one pass depends on another to be executed before it
     * e.g. InlineObject to Image conversion before Image is persisted or postprocessed
     * (otherwise there won't be Image elements at all)
     */
    public function priority(): int;
}
