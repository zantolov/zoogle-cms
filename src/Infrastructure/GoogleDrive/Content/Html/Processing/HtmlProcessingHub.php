<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDrive\Content\Html\Processing;

class HtmlProcessingHub
{
    /**
     * @param iterable<HtmlProcessor> $processors
     */
    public function __construct(private iterable $processors)
    {
    }

    public function process(string $html): string
    {
        foreach ($this->processors as $processor) {
            $html = $processor->process($html);
        }

        return $html;
    }
}
