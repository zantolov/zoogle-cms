<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\Symfony\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Zantolov\ZoogleCms\Domain\Document\Document;
use Zantolov\ZoogleCms\Domain\Document\DocumentElement;
use Zantolov\ZoogleCms\Infrastructure\GoogleDrive\Content\Document\DocumentFactory;
use Zantolov\ZoogleCms\Infrastructure\GoogleDrive\Content\Html\Converting\HtmlConverter;

class ZoogleCmsTwigExtension extends AbstractExtension
{
    public function __construct(
        private DocumentFactory $documentFactory,
        private HtmlConverter $htmlConverter
    ) {
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('zoogle_document', [$this, 'zoogleDocument']),
        ];
    }

    public function getFilters()
    {
        return [
            new TwigFilter('zoogle_html', [$this, 'zoogleHtml'], ['is_safe' => ['html']]),
            new TwigFilter('zoogle_document_html', [$this, 'documentHtml'], ['is_safe' => ['html']]),
            new TwigFilter('zoogle_element_html', [$this, 'elementHtml'], ['is_safe' => ['html']]),
        ];
    }

    public function zoogleDocument(string $url): Document
    {
        return $this->documentFactory->fromUrl($url);
    }

    public function zoogleHtml(Document|DocumentElement $item): string
    {
        if ($item instanceof Document) {
            return $this->documentHtml($item);
        }

        if ($item instanceof DocumentElement) {
            return $this->elementHtml($item);
        }

        throw new \InvalidArgumentException();
    }

    public function documentHtml(Document $document): string
    {
        return $this->htmlConverter->convert($document);
    }

    public function elementHtml(DocumentElement $element): string
    {
        return $this->htmlConverter->renderItem($element);
    }
}
