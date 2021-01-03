<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDrive\Content\Document\Processing;

use Zantolov\ZoogleCms\Domain\Document\Document;

class DocumentProcessor
{
    private iterable $passes;

    /**
     * @param iterable<ProcessingPass> $passes
     */
    public function __construct(iterable $passes)
    {
        $passes = iterator_to_array($passes);
        usort($passes, fn (ProcessingPass $pass1, ProcessingPass $pass2) => $pass1->priority() <=> $pass2->priority());
        $this->passes = $passes;
    }

    public function process(Document $document): Document
    {
        $processedDocument = clone($document);

        foreach ($this->passes as $pass) {
            $processedDocument = $pass->process(clone($processedDocument));
        }

        return $processedDocument;
    }
}
