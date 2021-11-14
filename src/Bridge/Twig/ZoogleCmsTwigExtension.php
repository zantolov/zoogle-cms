<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Bridge\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Zantolov\ZoogleCms\Model\Document\Document;
use Zantolov\ZoogleCms\Model\Document\DocumentElement;
use Zantolov\ZoogleCms\Service\Document\DocumentRepository;
use Zantolov\ZoogleCms\Service\Html\Converting\HtmlConverter;
use Zantolov\ZoogleCms\Service\Html\Processing\HtmlProcessingHub;

final class ZoogleCmsTwigExtension extends AbstractExtension
{
    public function __construct(
        private DocumentRepository $documentRepository,
        private HtmlConverter $htmlConverter,
        private HtmlProcessingHub $htmlProcessingHub
    ) {
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('zoogle_document', [$this, 'zoogleDocument']),
        ];
    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('zoogle_html', [$this, 'zoogleHtml'], ['is_safe' => ['html']]),
            new TwigFilter('zoogle_document_html', [$this, 'documentHtml'], ['is_safe' => ['html']]),
            new TwigFilter('zoogle_element_html', [$this, 'elementHtml'], ['is_safe' => ['html']]),
        ];
    }

    public function zoogleDocument(string $url): Document
    {
        return $this->documentRepository->getByUrl($url);
    }

    public function zoogleHtml(Document|DocumentElement $item): string
    {
        if ($item instanceof Document) {
            return $this->documentHtml($item);
        }

        \assert($item instanceof DocumentElement);

        return $this->elementHtml($item);
    }

    public function documentHtml(Document $document): string
    {
        $html = $this->htmlConverter->convert($document);

        return $this->htmlProcessingHub->process($html);
    }

    public function elementHtml(DocumentElement $element): string
    {
        return $this->htmlConverter->renderItem($element);
    }
}
