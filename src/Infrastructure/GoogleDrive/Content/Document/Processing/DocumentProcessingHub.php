<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDrive\Content\Document\Processing;

use Zantolov\ZoogleCms\Domain\Document\Document;

class DocumentProcessingHub
{
    private iterable $processors;

    /**
     * @param iterable<DocumentProcessor> $processors
     */
    public function __construct(iterable $processors)
    {
        $processors = iterator_to_array($processors);
        usort($processors, fn (DocumentProcessor $pass1, DocumentProcessor $pass2) => $pass1->priority() <=> $pass2->priority());
        $this->processors = $processors;
    }

    public function process(Document $document): Document
    {
        $processedDocument = clone($document);
        foreach ($this->processors as $processor) {
            $processedDocument = $processor->process(clone($processedDocument));
        }

        return $processedDocument;
    }
}
