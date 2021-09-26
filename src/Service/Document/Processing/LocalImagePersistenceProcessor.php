<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Service\Document\Processing;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Zantolov\ZoogleCms\Model\Document\Document;
use Zantolov\ZoogleCms\Model\Document\DocumentElement;
use Zantolov\ZoogleCms\Model\Document\Image as ImageElement;

final class LocalImagePersistenceProcessor extends AbstractElementDocumentProcessor
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
        $filename = \Safe\sprintf('%s.%s', $imageHash, $extension);

        // Warm up cache
        $this->cache->get($filename, static fn () => \Safe\file_get_contents($element->src));

        $proxyedImageUrl = $this->router->generate(
            'zoogle_cms_image',
            ['filename' => $filename],
            RouterInterface::ABSOLUTE_URL
        );
        $element = $element->withSrc($proxyedImageUrl);

        return $element;
    }
}
