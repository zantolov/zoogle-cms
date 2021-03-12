<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDrive\Content\Document\Processing;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Zantolov\ZoogleCms\Domain\Document\Document;
use Zantolov\ZoogleCms\Domain\Document\DocumentElement;
use Zantolov\ZoogleCms\Domain\Document\Image;
use Zantolov\ZoogleCms\Domain\Document\Image as ImageElement;

class LocalImagePersistenceProcessor extends AbstractElementDocumentProcessor
{
    public function __construct(private CacheInterface $cache, private RouterInterface $router)
    {
    }

    public function priority(): int
    {
        return 100;
    }

    protected function supports(DocumentElement $element): bool
    {
        return $element instanceof ImageElement;
    }

    protected function processElement(DocumentElement $element, Document $document): DocumentElement
    {
        \assert($element instanceof ImageElement);

        $imageHash = sha1($document->id.$element->id);
        $pathParts = pathinfo($element->src);
        $extension = $pathParts['extension'] ?? 'jpg';
        $filename = sprintf('%s.%s', $imageHash, $extension);

        $cachedItem = $this->cache->get($filename, function () use ($filename, $element) {
            return file_get_contents($element->src);
        });

        $proxyedImageUrl = $this->router->generate(
            'zoogle_cms_image',
            ['filename' => $filename],
            RouterInterface::ABSOLUTE_URL
        );
        $element = $element->withSrc($proxyedImageUrl);

        return $element;
    }
}
