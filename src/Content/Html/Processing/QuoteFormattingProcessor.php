<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Content\Html\Processing;

class QuoteFormattingProcessor implements HtmlProcessor
{
    public function process(string $html): string
    {
        preg_match_all('/<p>\[quote\](.*?)<\/p>/ms', $html, $quotes);

        return str_replace(
            $quotes[0],
            array_map(static fn (string $line): string => sprintf('<blockquote>%s</blockquote>', mb_trim($line)), $quotes[1]),
            $html,
        );
    }
}
