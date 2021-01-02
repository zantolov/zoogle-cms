<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDrive\Content\Converting\Document;

/**
 * @internal
 */
abstract class AbstractContentElementConverter implements ElementConverter
{
    /**
     * Joins all content in a given paragraph without any formatting
     */
    protected function getUnformattedParagraphContent(\Google_Service_Docs_Paragraph $paragraph): string
    {
        return array_reduce(
            $paragraph->getElements(),
            fn (
                string $carry,
                \Google_Service_Docs_ParagraphElement $element
            ) => $carry.(trim($element?->getTextRun()?->getContent() ?: '')),
            ''
        );
    }

    protected function getFormattedParagraphContent(\Google_Service_Docs_Paragraph $paragraph): string
    {
        return array_reduce(
            $paragraph->getElements(),
            fn (
                string $carry,
                \Google_Service_Docs_ParagraphElement $element
            ) => $carry.$this->getFormattedParagraphElementContent($element),
            ''
        );
    }

    private function getFormattedParagraphElementContent(\Google_Service_Docs_ParagraphElement $element): string
    {
        if (null === $element->getTextRun() || empty(trim($element->getTextRun()))) {
            return '';
        }

        $type = $element->getTextRun()->getTextStyle();
        $content = $element->getTextRun()->getContent();

        if ($type->getLink()) {
            $link = $type->getLink();
            $url = $link->getUrl();

            return sprintf('<a href="%s">%s</a>', $url, $content);
        }

        // @todo bold
        // @todo italic
        // @todo underline

        return $content;
    }
}
