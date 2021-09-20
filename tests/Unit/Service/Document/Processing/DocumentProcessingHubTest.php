<?php

declare(strict_types=1);

namespace Tests\Zantolov\ZoogleCms\Service\Document\Processing;

use PHPUnit\Framework\TestCase;
use Zantolov\ZoogleCms\Model\Document\Document;
use Zantolov\ZoogleCms\Service\Document\Processing\DocumentProcessingHub;
use Zantolov\ZoogleCms\Service\Document\Processing\DocumentProcessor;

/**
 * @internal
 * @coversNothing
 *
 * @small
 */
final class DocumentProcessingHubTest extends TestCase
{
    public function test_it_sorts_processors_by_priority(): void
    {
        $executionOrder = [];
        $processor1 = $this->createMock(DocumentProcessor::class);
        $processor1->expects($this->atLeastOnce())->method('priority')->willReturn(1);
        $processor1->expects($this->once())
            ->method('process')
            ->willReturnCallback(static function (Document $document) use (&$executionOrder) {
                $executionOrder[] = 1;

                return $document;
            });
        $processor2 = $this->createMock(DocumentProcessor::class);
        $processor2->expects($this->atLeastOnce())->method('priority')->willReturn(2);
        $processor2->expects($this->once())
            ->method('process')
            ->willReturnCallback(static function (Document $document) use (&$executionOrder) {
                $executionOrder[] = 2;

                return $document;
            });
        $processor3 = $this->createMock(DocumentProcessor::class);
        $processor3->expects($this->atLeastOnce())->method('priority')->willReturn(3);
        $processor3->expects($this->once())
            ->method('process')
            ->willReturnCallback(static function (Document $document) use (&$executionOrder) {
                $executionOrder[] = 3;

                return $document;
            });

        $documentProcessingHub = new DocumentProcessingHub(new \ArrayIterator([$processor3, $processor1, $processor2]));
        $documentProcessingHub->process($this->createMock(Document::class));

        $this->assertSame([1, 2, 3], $executionOrder);
    }
}
