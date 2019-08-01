<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Repository;

use Zantolov\ZoogleCms\Domain\Article;
use Zantolov\ZoogleCms\Domain\ArticleRepository as ArticleRepositoryInterface;
use Zantolov\ZoogleCms\Domain\ValueObject\ArticleId;
use Zantolov\ZoogleCms\Domain\ValueObject\CategoryId;
use Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Client\GoogleDriveClient;
use Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Configuration\Configuration;
use Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Factory\ArticleFactory;

final class ArticleRepository implements ArticleRepositoryInterface
{
    /** @var Configuration */
    private $configuration;

    /** @var GoogleDriveClient */
    private $client;

    /** @var ArticleFactory */
    private $articleFactory;

    public function __construct(Configuration $configuration, GoogleDriveClient $client, ArticleFactory $articleFactory)
    {
        $this->configuration = $configuration;
        $this->client = $client;
        $this->articleFactory = $articleFactory;
    }

    private function sort(array &$items): void
    {
        uasort($items, function (Article $a, Article $b) {
            return $b->getPublishDateTime() <=> $a->getPublishDateTime();
        });
    }

    /** @return Article[] */
    public function findAllInCategory(CategoryId $categoryId): array
    {
        $files = $this->client->listDocs($categoryId->toString());
        $articles = array_map([$this->articleFactory, 'make'], $files);
        $this->sort($articles);

        return $articles;
    }

    public function get(ArticleId $articleId): Article
    {
        $doc = $this->client->getFile($articleId->toString());

        return $this->articleFactory->make($doc);
    }

    /** @return Article|null */
    public function findBySlugAndCategory(string $slug, CategoryId $categoryId): ?Article
    {
        $files = $this->client->listDocs($categoryId->toString());
        foreach ($files as $file) {
            if ($slug === $file->getName()) {
                return $this->articleFactory->make($file);
            }
        }

        return null;
    }

    public function findBySlug(string $slug): ?Article
    {
        $files = $this->client->listAllDocs();
        foreach ($files as $file) {
            if ($slug === $file->getName()) {
                return $this->articleFactory->make($file);
            }
        }

        return null;
    }
}
