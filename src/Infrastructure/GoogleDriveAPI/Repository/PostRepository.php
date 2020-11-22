<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Repository;

use Psr\Cache\CacheItemPoolInterface;
use Zantolov\ZoogleCms\Application\FindPosts\FindPost;
use Zantolov\ZoogleCms\Application\FindPosts\FindPostsInCategory;
use Zantolov\ZoogleCms\Application\FindPosts\FindUncategorizedPosts;
use Zantolov\ZoogleCms\Domain\Category\CategoryId;
use Zantolov\ZoogleCms\Domain\Post\Post;
use Zantolov\ZoogleCms\Domain\Post\PostId;
use Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Client\GoogleDriveClient;
use Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Configuration\Configuration;
use Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Factory\ArticleFactory;

final class PostRepository implements FindPost, FindPostsInCategory, FindUncategorizedPosts
{
    private Configuration $configuration;
    private GoogleDriveClient $client;
    private ArticleFactory $articleFactory;
    private CacheItemPoolInterface $cache;

    public function __construct(
        Configuration $configuration,
        GoogleDriveClient $client,
        ArticleFactory $articleFactory,
        CacheItemPoolInterface $cache
    ) {
        $this->configuration = $configuration;
        $this->client = $client;
        $this->articleFactory = $articleFactory;
        $this->cache = $cache;
    }

    private function sort(array &$items): void
    {
        uasort($items, function (Post $a, Post $b) {
            return $b->publishDateTime <=> $a->publishDateTime;
        });
    }

    public function get(PostId $articleId): Post
    {
        $doc = $this->client->getFile($articleId->value);

        return $this->articleFactory->make($doc);
    }

    public function findInCategoryBySlug(CategoryId $categoryId, string $slug): ?Post
    {
        $files = $this->client->listDocs($categoryId->value);
        foreach ($files as $file) {
            if ($slug === $file->getName()) {
                return $this->articleFactory->make($file);
            }
        }

        return null;
    }

    public function findBySlug(string $slug): ?Post
    {
        $files = $this->client->listAllDocs();
        foreach ($files as $file) {
            if ($slug === $file->getName()) {
                return $this->articleFactory->make($file);
            }
        }

        return null;
    }

    public function findById(string $id): ?Post
    {
        $file = $this->client->getFile($id);

        return $this->articleFactory->make($file);
    }

    public function findUncategorized(): array
    {
        $topLevelCategory = new CategoryId($this->configuration->getRootDirectoryId());

        return $this->allInCategory($topLevelCategory);
    }

    public function allInCategory(CategoryId $id): array
    {
        $files = $this->client->listDocs($id->value);
        $articles = array_map([$this->articleFactory, 'make'], $files);
        $this->sort($articles);

        return $articles;
    }
}
