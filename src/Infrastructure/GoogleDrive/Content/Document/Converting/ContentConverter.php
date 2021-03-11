<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDrive\Content\Document\Converting;

use Zantolov\ZoogleCms\Domain\Document\ListItem;
use Zantolov\ZoogleCms\Domain\Document\Paragraph;
use Zantolov\ZoogleCms\Domain\Document\Text;

/**
 * @internal
 */
class ContentConverter extends AbstractContentElementConverter
{
    /** @return ContentElement[] */
    public function convert(\Google_Service_Docs_Paragraph $paragraph): array
    {
        $listId = null;
        $nestingLevel = null;

        if ($bullet = $paragraph->getBullet()) {
            $listId = $bullet->getListId();
            $nestingLevel = $bullet->getNestingLevel();
        }

        $paragraphElements = $this->convertParagraphElements($paragraph->getElements());
        if (empty($paragraphElements)) {
            throw new \RuntimeException('Empty result set after conversion. Tweak the supports method');
        }

        // If the paragraph defines a list, wrap all the content in a ListItem that will later be joined in a list.
        if (null !== $listId) {
            return [new ListItem($listId, $paragraphElements, $nestingLevel ?? 1)];
        }

        return [new Paragraph($paragraphElements)];
    }

    public function supports(\Google_Service_Docs_Paragraph $paragraph): bool
    {
        return 'NORMAL_TEXT' === $paragraph->getParagraphStyle()?->getNamedStyleType()
            && count($this->convertParagraphElements($paragraph->getElements())) > 0;
    }

    /**
     * @param \Google_Service_Docs_ParagraphElement[] $elements
     * @return array<int, Text|null>
     */
    private function convertParagraphElements(array $elements): array
    {
        $paragraphElements = array_map(
            fn (\Google_Service_Docs_ParagraphElement $element) => $this->convertParagraphElement($element),
            $elements
        );
        $paragraphElements = array_filter($paragraphElements);

        return $paragraphElements;
    }

    private function convertParagraphElement(\Google_Service_Docs_ParagraphElement $element): ?Text
    {
        // Skip empty content
        $textRun = $element->getTextRun();
        $content = $textRun?->getContent();
        if (null === $textRun || empty(trim($content))) {
            return null;
        }

        $style = $textRun->getTextStyle();

        return new Text(
            $content,
            $style?->getBold() ?? false,
            $style?->getItalic() ?? false,
            $style?->getUnderline() ?? false,
            $style?->getLink()?->getUrl() ?? null
        );
    }
}
