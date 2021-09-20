<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Service\Html\Converting;

use Zantolov\ZoogleCms\Model\Document\Document;
use Zantolov\ZoogleCms\Model\Document\DocumentElement;
use Zantolov\ZoogleCms\Model\Document\DocumentList;
use Zantolov\ZoogleCms\Model\Document\Heading;
use Zantolov\ZoogleCms\Model\Document\Image;
use Zantolov\ZoogleCms\Model\Document\ListItem;
use Zantolov\ZoogleCms\Model\Document\Metadata;
use Zantolov\ZoogleCms\Model\Document\Paragraph;
use Zantolov\ZoogleCms\Model\Document\Subtitle;
use Zantolov\ZoogleCms\Model\Document\Text;
use Zantolov\ZoogleCms\Model\Document\Title;

final class HtmlConverter
{
    public function convert(Document $document): string
    {
        $string = '';
        foreach ($document->elements as $item) {
            $string .= $this->renderItem($item);
        }

        return $string;
    }

    public function renderItem(DocumentElement $item): string
    {
        if ($item instanceof Heading) {
            return \Safe\sprintf('<h%s>%s</h%s>', $item->level, $item->value, $item->level);
        }

        if ($item instanceof Text) {
            $value = $item->value;
            $value = rtrim($value, "\v");
            $value = str_replace("\v", '<br/>', $value);

            if ($item->link) {
                return \Safe\sprintf('<a href="%s">%s</a>', $item->link, $value);
            }

            if ($item->italic) {
                $value = \Safe\sprintf('<i>%s</i>', $value);
            }

            if ($item->underline) {
                $value = \Safe\sprintf('<u>%s</u>', $value);
            }

            if ($item->bold) {
                $value = \Safe\sprintf('<b>%s</b>', $value);
            }

            return $value;
        }

        if ($item instanceof Paragraph) {
            $content = array_reduce(
                $item->texts,
                fn (string $carry, Text $text) => $carry.$this->renderItem($text),
                ''
            );

            return \Safe\sprintf('<p>%s</p>', $content);
        }

        if ($item instanceof ListItem) {
            $content = array_reduce(
                $item->texts,
                fn (string $carry, Text $text) => $carry.$this->renderItem($text),
                ''
            );

            return \Safe\sprintf('<li>%s</li>', $content);
        }

        if ($item instanceof DocumentList) {
            $listItems = array_map(fn (ListItem $item) => $this->renderItem($item), $item->items);
            $listItems = implode("\n", $listItems);

            return match ($item->type) {
                DocumentList::TYPE_ORDERED => \Safe\sprintf('<ol>%s</ol>', $listItems),
                DocumentList::TYPE_UNORDERED => \Safe\sprintf('<ul>%s</ul>', $listItems),
                default => throw new \InvalidArgumentException('Unsupported list type given: '.$item->type)
            };
        }

        if ($item instanceof Image) {
            return \Safe\sprintf(
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

        if ($item instanceof Subtitle) {
            return '';
        }

        throw new \InvalidArgumentException('Unsupported element given: '.$item::class);
    }
}
