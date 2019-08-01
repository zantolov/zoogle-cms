<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Repository;

use Zantolov\ZoogleCms\Domain\Category as CategoryInterface;
use Zantolov\ZoogleCms\Domain\CategoryRepository as CategoryRepositoryInterface;
use Zantolov\ZoogleCms\Domain\ValueObject\CategoryId;
use Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Client\GoogleDriveClient;
use Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Configuration\Configuration;
use Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Model\Category;

final class CategoryRepository implements CategoryRepositoryInterface
{
    /** @var Configuration */
    private $configuration;

    /** @var GoogleDriveClient */
    private $client;

    public function __construct(Configuration $configuration, GoogleDriveClient $client)
    {
        $this->configuration = $configuration;
        $this->client = $client;
    }

    public function get(CategoryId $categoryId): CategoryInterface
    {
        $googleDriveFile = $this->client->getFile($categoryId->toString());

        return Category::fromGoogleDriveFile($googleDriveFile);
    }

    /** @return CategoryInterface[] */
    public function findAll(): array
    {
        $fetchChildrenCategories = function (string $id) {
            $directories = [];
            $directChildren = $this->client->listDirectories($id);
            foreach ($directChildren as $directChild) {
                $category = Category::fromGoogleDriveFile(
                    $directChild,
                    $id
                );
                $directories[] = $category;
            }

            return $directories;
        };

        $categories = [];

        $rootDirectories = $this->client->listRootDirectories();

        foreach ($rootDirectories as $rootDirectory) {
            $category = Category::fromGoogleDriveFile($rootDirectory, null);
            $categories[] = $category;

            $childrenCategories = $fetchChildrenCategories($category->getId()->toString());
            $categories = array_merge($categories, $childrenCategories);
        }

        return $categories;
    }


    public function findBySlug(string $slug): ?CategoryInterface
    {
        $categories = $this->findAll();
        foreach ($categories as $category) {
            if ($category->getSlug() === $slug) {
                return $category;
            }
        }

        return null;
    }
}
