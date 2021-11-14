<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Service\Document\Converting;

use Zantolov\ZoogleCms\Model\Google\Paragraph;
use Zantolov\ZoogleCms\Model\Google\ParagraphElement;
use Zantolov\ZoogleCms\Model\Google\TextRun;

/**
 * @internal
 */
abstract class AbstractContentElementConverter implements ElementConverter
{
    /**
     * Joins all content in a given paragraph without any formatting.
     */
    protected function getUnformattedParagraphContent(Paragraph $paragraph): string
    {
        return array_reduce(
            $paragraph->getElements(),
            static fn (
                string $carry,
                ParagraphElement $element
            ): string => $carry.(trim($element->getTextRun()?->getContent() ?: '')),
            ''
        );
    }

    protected function getFormattedParagraphContent(Paragraph $paragraph): string
    {
        return array_reduce(
            $paragraph->getElements(),
            fn (
                string $carry,
                ParagraphElement $element
            ): string => $carry.$this->getFormattedParagraphElementContent($element),
            ''
        );
    }

    private function getFormattedParagraphElementContent(ParagraphElement $element): string
    {
        $textRun = $element->getTextRun();
        if (!$textRun instanceof TextRun) {
            return '';
        }

        $content = $textRun->getContent();
        if ($content === null || empty(trim($content))) {
            return '';
        }

        $url = $textRun->getTextStyle()?->getLinkUrl();
        if ($url !== null) {
            return \Safe\sprintf('<a href="%s">%s</a>', $url, $content);
        }

        // @todo bold
        // @todo italic
        // @todo underline

        return $content;
    }
}
