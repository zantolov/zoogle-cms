<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Service\Document\Processing;

use Zantolov\ZoogleCms\Model\Document\Document;

final class DocumentProcessingHub
{
    /**
     * @var iterable<DocumentProcessor>
     */
    private iterable $processors;

    /**
     * @param \Traversable<DocumentProcessor> $processors
     */
    public function __construct(\Traversable $processors)
    {
        $processors = iterator_to_array($processors);
        \Safe\usort(
            $processors,
            static fn (DocumentProcessor $pass1, DocumentProcessor $pass2): int => $pass1->priority() <=> $pass2->priority()
        );
        $this->processors = $processors;
    }

    public function process(Document $document): Document
    {
        $processedDocument = clone $document;
        foreach ($this->processors as $processor) {
            $processedDocument = $processor->process(clone $processedDocument);
        }

        return $processedDocument;
    }
}
