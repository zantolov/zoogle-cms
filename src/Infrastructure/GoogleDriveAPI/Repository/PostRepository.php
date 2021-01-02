<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Repository;

use Cocur\Chain\Chain;
use Zantolov\ZoogleCms\Application\FindPosts\FindPost;
use Zantolov\ZoogleCms\Application\FindPosts\FindPostsInCategory;
use Zantolov\ZoogleCms\Application\FindPosts\FindUncategorizedPosts;
use Zantolov\ZoogleCms\Domain\Category\CategoryId;
use Zantolov\ZoogleCms\Domain\Post\Author;
use Zantolov\ZoogleCms\Domain\Post\Post;
use Zantolov\ZoogleCms\Domain\Post\PostId;
use Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Client\GoogleDriveClient;
use Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Configuration\Configuration;
use Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Factory\PostFactory;

final class PostRepository implements FindPost, FindPostsInCategory, FindUncategorizedPosts
{
    public function __construct(
        private Configuration $configuration,
        private GoogleDriveClient $client,
        private PostFactory $postFactory
    ) {
    }

    // @todo this has to be public so that it's accessible from the chain callback
    public function sort(Post $a, Post $b): int
    {
        return $b->publishingDateTime <=> $a->publishingDateTime;
    }

    public function get(PostId $articleId): Post
    {
        $file = $this->client->getFile($articleId->value);

        return $this->postFactory->make($file);
    }

    public function findInCategoryBySlug(CategoryId $categoryId, string $slug): ?Post
    {
        $files = $this->client->listDocs($categoryId->value);
        $match = Chain::create($files)
            ->find(fn ($file) => $slug === $file->getName());

        return $match ? $this->postFactory->make($match) : null;
    }

    public function findBySlug(string $slug): ?Post
    {
        $files = $this->client->listAllDocs();
        $match = Chain::create($files)
            ->find(fn ($file) => $slug === $file->getName());

        return $match ? $this->postFactory->make($match) : null;
    }

    public function findById(PostId $id): ?Post
    {
        try {
            $this->get($id);
            $file = $this->client->getFile($id->value);

            return $this->postFactory->make($file);
        } catch (Google\Exception $e) {
            return null;
        }
    }

    public function findByAuthor(Author $author): ?Post
    {
        $posts = $this->all();

        return Chain::create($posts)
            ->filter(fn (Post $post) => null !== $post->author && $post->author->equals($author))
            ->filter(fn (Post $post) => $post->isPublished(new \DateTimeImmutable()))
            ->sort([$this, 'sort'])
            ->values()
            ->array;

        return array_values($authorPosts);
    }

    public function findUncategorized(): array
    {
        $topLevelCategory = new CategoryId($this->configuration->rootDirectoryId);

        return $this->allInCategory($topLevelCategory);
    }

    public function allInCategory(CategoryId $id): array
    {
        $files = $this->client->listDocs($id->value);

        return Chain::create($files)
            ->map([$this->postFactory, 'make'])
            ->filter(fn (Post $post) => $post->isPublished(new \DateTimeImmutable('now')))
            ->sort([$this, 'sort'])
            ->values()
            ->array;
    }

    public function all(): array
    {
        $files = $this->client->listAllDocs();

        return Chain::create($files)
            ->map([$this->postFactory, 'make'])
            ->filter(fn (Post $post) => $post->isPublished(new \DateTimeImmutable()))
            ->sort([$this, 'sort'])
            ->values()
            ->array;
    }
}
