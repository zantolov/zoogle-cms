<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Content\Processing;

use DOMElement;
use Symfony\Component\DomCrawler\Crawler;
use Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Content\Processing\ProcessorInterface;

final class StyleRemover implements ProcessorInterface
{
    public function process(string $html): string
    {
        // Remove empty tags
        $regex = '/<([^>\s]+)[^>]*>(?:\s*(?:<br \/>|&nbsp;|&thinsp;|&ensp;|&emsp;|&#8201;|&#8194;|&#8195;)\s*)*<\/\1>/';
        $html = preg_replace($regex, '', $html);

        $crawler = new Crawler($html);
        $crawler = $crawler->filter('body')->first();

        $this->removeExtraTags($crawler);
        $this->removeSpans($crawler);

        foreach ($crawler->children() as $node) {
            $this->cleanUpAttributes($node);
        }

        return $crawler->html();
    }

    private function cleanUpAttributes(DOMElement $node)
    {
        $node->removeAttribute('id');
        $node->removeAttribute('style');
        $node->removeAttribute('class');

        foreach ($node->childNodes as $child) {
            if ($child instanceof DOMElement) {
                $this->cleanUpAttributes($child);
            }
        }
    }

    private function removeNode(DOMElement $node)
    {
        $node->parentNode->removeChild($node);
    }

    private function removeSpans(Crawler $crawler)
    {
        foreach ($crawler->filter('* > span') as $domElement) {
            $parent = $domElement->parentNode;

            foreach ($domElement->childNodes as $childNode) {
                $parent->appendChild($childNode);
            }

            $this->removeNode($domElement);
        }
    }

    private function removeExtraTags(Crawler $crawler)
    {
        $meta = $crawler->filter('meta, style');
        foreach ($meta as $metaNode) {
            $this->removeNode($metaNode);
        }
    }
}
