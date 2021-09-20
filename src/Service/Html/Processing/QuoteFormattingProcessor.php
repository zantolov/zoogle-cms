<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Service\Html\Processing;

final class QuoteFormattingProcessor implements HtmlProcessor
{
    public function process(string $html): string
    {
        \Safe\preg_match_all('/<p>\[quote\](.*?)<\/p>/ms', $html, $quotes);
        $html = str_replace(
            $quotes[0],
            array_map(static fn (string $line) => \Safe\sprintf('<blockquote>%s</blockquote>', trim($line)), $quotes[1]),
            $html
        );

        return $html;
    }
}
