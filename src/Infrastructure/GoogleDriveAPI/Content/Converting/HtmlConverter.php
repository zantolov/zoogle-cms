<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Content\Converting;

use Zantolov\ZoogleCms\Domain\Document\ContentElement;
use Zantolov\ZoogleCms\Domain\Document\Document;
use Zantolov\ZoogleCms\Domain\Document\DocumentElement;
use Zantolov\ZoogleCms\Domain\Document\DocumentList;
use Zantolov\ZoogleCms\Domain\Document\Heading;
use Zantolov\ZoogleCms\Domain\Document\Image;
use Zantolov\ZoogleCms\Domain\Document\ListItem;
use Zantolov\ZoogleCms\Domain\Document\Metadata;
use Zantolov\ZoogleCms\Domain\Document\Paragraph;
use Zantolov\ZoogleCms\Domain\Document\Text;
use Zantolov\ZoogleCms\Domain\Document\Title;

class HtmlConverter
{
    public function convert(Document $document): string
    {
        $string = '';

        foreach ($document->items as $item) {
            $string .= $this->renderItem($item);
        }

        // @todo plugins

        return $string;
    }

    private function renderItem(DocumentElement $item): string
    {
        if ($item instanceof Heading) {
            return sprintf('<h%s>%s</h%s>', $item->level, $item->value, $item->level);
        }

        if ($item instanceof Text) {
            $value = $item->value;

            if ($item->link) {
                return sprintf('<a href="%s">%s</a>', $item->link, $value);
            }

            if ($item->italic) {
                $value = sprintf('<i>%s</i>', $value);
            }

            if ($item->underline) {
                $value = sprintf('<u>%s</u>', $value);
            }

            if ($item->bold) {
                $value = sprintf('<b>%s</b>', $value);
            }

            return $value;
        }

        if ($item instanceof Paragraph) {
            $content = array_reduce(
                $item->texts,
                fn (string $carry, Text $text) => $carry.$this->renderItem($text),
                ''
            );

            return sprintf('<p>%s</p>', $content);
        }

        if ($item instanceof ListItem) {
            $content = array_reduce(
                $item->texts,
                fn (string $carry, Text $text) => $carry.$this->renderItem($text),
                ''
            );

            return sprintf('<li>%s</li>', $content);
        }

        if ($item instanceof DocumentList) {
            $listItems = array_map(fn (ListItem $item) => $this->renderItem($item), $item->items);
            $listItems = implode("\n", $listItems);

            return match ($item->type) {
                DocumentList::TYPE_ORDERED => sprintf('<ol>%s</ol>', $listItems),
                DocumentList::TYPE_UNORDERED => sprintf('<ul>%s</ul>', $listItems),
                default => throw new \InvalidArgumentException('Unsupported list type given: '.$item->type)
            };
        }

        if ($item instanceof Image) {
            return sprintf(
                '<img src="%s" alt="%s" data-description="%s"/>',
                $item->src,
                $item->alt,
                $item->description
            );
        }

        // Skip these elements from the content
        if ($item instanceof Metadata) {
            return '';
        }

        if ($item instanceof Title) {
            return '';
        }

        throw new \InvalidArgumentException('Unsupported element given: '.get_class($item));
    }
}
