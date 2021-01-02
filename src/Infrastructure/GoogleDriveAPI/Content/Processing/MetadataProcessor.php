<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Content\Processing;

use DOMElement;
use Symfony\Component\DomCrawler\Crawler;
use Zantolov\ZoogleCms\Domain\Document\Metadata;

final class MetadataProcessor
{
    private function removeNode(?DOMElement $node)
    {
        if (null === $node) {
            return;
        }

        $node->parentNode->removeChild($node);
    }

    /**
     * Extract title of the article (i.e. the text styled with the "Title" Google Doc style), remove it
     * from the document and return the value.
     *
     * @param string $html HTML to be processed and updated
     * @return string HTML without the Title element
     */
    public function extractTitle(string &$html): string
    {
        $crawler = new Crawler($html);
        $titleNode = $crawler->filter('.title')->first();
        if ($titleNode->count() === 0) {
            return 'Untitled';
        }

        $title = $titleNode->text();
        $this->removeNode($titleNode->getNode(0));
        $html = $crawler->html();

        return $title;
    }

    /**
     * Extract the metadata of the document located at the end of the content to be published.
     * Everything after the H1 heading with content "Meta" will be removed from the resulting HTML
     * Metadata values will be key-value pairs of list items, separated by colon
     *
     * Supported meta:
     * - publish: {datetime string value}
     * - author: {author string caption}
     * - tags: {comma separated list of tags}
     *
     * @param string $html HTML to be processed and updated
     * @return array key-value pair of recognized meta data
     */
    public function extractMeta(string &$html): Metadata
    {
        $crawler = new Crawler($html);
        $metaList = $crawler->filter('h1:contains("Meta") + ul')->first();

        if (0 === $metaList->count()) {
            return new Metadata([]);
        }

        $meta = [];
        foreach ($metaList->filter('li') as $metaNode) {
            $value = explode(':', $metaNode->nodeValue, 2);
            $metaName = mb_strtolower(trim($value[0]));
            $meta[$metaName] = trim($value[1] ?? '');
        }

        // Remove META heading and all following siblings
        foreach ($crawler->filter('h1:contains("Meta") ~ *') as $node) {
            $this->removeNode($node);
        }
        $this->removeNode($crawler->filter('body > h1:contains("Meta")')->first()->getNode(0));

        $html = $crawler->html();

        if (isset($meta['tags']) && is_string($meta['tags']) && mb_strlen(trim($meta['tags'])) > 0) {
            $tags = explode(',', $meta['tags']);
            $tags = array_map(fn (string $item) => trim($item), $tags);
            $meta['tags'] = $tags;
        }

        return new Metadata($meta);
    }

    /**
     * Extract the first found Image so that it can be used as leading article image.
     * Remove the `<img>` element so that the image can be rendered separate from the Google Doc HTML
     *
     * @param string $html HTML to be processed and updated
     * @return string URL of the first found Image
     */
    public function extractFirstImageUrl(string &$html): ?string
    {
        $crawler = new Crawler($html);
        $images = $crawler->filter('body img');

        if (0 === $images->count()) {
            return null;
        }

        $firstImage = $images->first();
        $url = $firstImage->attr('src');
        $this->removeNode($firstImage->getNode(0));
        $html = $crawler->html();

        return $url;
    }
}