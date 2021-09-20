<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Service\GoogleDrive\Client;

final class GoogleDriveClientFactory
{
    public function __construct(private BaseGoogleDriveClient $client, private CachedGoogleDriveClient $cachedClient)
    {
    }

    public function create(bool $useCache): GoogleDriveClient
    {
        return $useCache ? $this->cachedClient : $this->client;
    }
}
