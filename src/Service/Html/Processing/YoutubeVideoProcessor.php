<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Service\Html\Processing;

final class YoutubeVideoProcessor implements HtmlProcessor
{
    public function process(string $html): string
    {
        $youtubeEmbedTemplate = <<<'HTML'
            <iframe
            class="youtube-video"
            src="https://www.youtube.com/embed/%s"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen></iframe>
            HTML;

        \Safe\preg_match_all(
            '/<p>[a-zA-Z\/\/:\.]*youtu(?:be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)[.\s]*<\/p>/ims',
            $html,
            $youtubeVideos
        );

        return str_replace(
            $youtubeVideos[0],
            array_map(
                static fn (string $code): string => \Safe\sprintf(
                    $youtubeEmbedTemplate,
                    trim($code)
                ),
                $youtubeVideos[1]
            ),
            $html
        );
    }
}
