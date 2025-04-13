<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Content\Html\Converting;

use Zantolov\Zoogle\Model\Model\Document\Document;
use Zantolov\Zoogle\Model\Model\Document\DocumentElement;
use Zantolov\Zoogle\Model\Model\Document\DocumentList;
use Zantolov\Zoogle\Model\Model\Document\Heading;
use Zantolov\Zoogle\Model\Model\Document\Image;
use Zantolov\Zoogle\Model\Model\Document\ListItem;
use Zantolov\Zoogle\Model\Model\Document\Metadata;
use Zantolov\Zoogle\Model\Model\Document\Paragraph;
use Zantolov\Zoogle\Model\Model\Document\Subtitle;
use Zantolov\Zoogle\Model\Model\Document\Text;
use Zantolov\Zoogle\Model\Model\Document\Title;

class HtmlConverter
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
            return sprintf('<h%s>%s</h%s>', $item->level, $item->value, $item->level);
        }

        if ($item instanceof Text) {
            $value = $item->value;
            $value = mb_rtrim($value, "\v");
            $value = str_replace("\v", '<br/>', $value);

            if ($item->link !== null && $item->link !== '' && $item->link !== '0') {
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
                fn (string $carry, Text $text): string => $carry.$this->renderItem($text),
                '',
            );

            return sprintf('<p>%s</p>', $content);
        }

        if ($item instanceof ListItem) {
            $content = array_reduce(
                $item->texts,
                fn (string $carry, Text $text): string => $carry.$this->renderItem($text),
                '',
            );

            return sprintf('<li>%s</li>', $content);
        }

        if ($item instanceof DocumentList) {
            $listItems = array_map(fn (ListItem $item): string => $this->renderItem($item), $item->items);
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
                $item->description,
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
