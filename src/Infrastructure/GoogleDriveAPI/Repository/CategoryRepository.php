<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Repository;

use Zantolov\ZoogleCms\Application\FindCategories\FindCategories;
use Zantolov\ZoogleCms\Application\FindCategories\FindCategory;
use Zantolov\ZoogleCms\Application\FindCategories\FindChildCategories;
use Zantolov\ZoogleCms\Domain\Category\Category;
use Zantolov\ZoogleCms\Domain\Category\CategoryId;
use Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Client\GoogleDriveClient;
use Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Configuration\Configuration;
use Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Factory\CategoryFactory;

final class CategoryRepository implements FindCategories, FindChildCategories, FindCategory
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

    public function get(CategoryId $categoryId): Category
    {
        $googleDriveFile = $this->client->getFile($categoryId->value);

        return (new CategoryFactory())->fromGoogleDriveFile($googleDriveFile);
    }

    /** @return Category[] */
    public function all(): array
    {
        $fetchChildrenCategories = function (CategoryId $id) {
            $directories = [];
            $directChildren = $this->client->listDirectories($id->value);
            foreach ($directChildren as $directChild) {
                $category = (new CategoryFactory())->fromGoogleDriveFile($directChild, $id->value);
                $directories[] = $category;
            }

            return $directories;
        };

        $categories = [];

        $rootDirectories = $this->client->listRootDirectories();

        foreach ($rootDirectories as $rootDirectory) {
            $category = (new CategoryFactory())->fromGoogleDriveFile($rootDirectory);
            $categories[] = $category;

            $childrenCategories = $fetchChildrenCategories($category->id);
            $categories = array_merge($categories, $childrenCategories);
        }

        return $categories;
    }


    public function find(string $slug): ?Category
    {
        $categories = $this->all();
        foreach ($categories as $category) {
            if ($category->slug === $slug) {
                return $category;
            }
        }

        return null;
    }

    public function findChildCategories(Category $category): array
    {
        $directories = [];
        $directChildren = $this->client->listDirectories($category->id->value);
        foreach ($directChildren as $directChild) {
            $category = (new CategoryFactory())->fromGoogleDriveFile($directChild, $id->value);
            $directories[] = $category;
        }

        return $directories;
    }
}
