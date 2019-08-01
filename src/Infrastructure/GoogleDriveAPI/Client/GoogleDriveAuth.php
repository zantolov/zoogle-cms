<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Client;

use Assert\Assertion;

final class GoogleDriveAuth
{
    /** @var string  */
    private $clientId;

    /** @var string  */
    private $authConfigPath;

    public function __construct(string $clientId, string $authConfigPath)
    {
        $this->clientId = $clientId;
        Assertion::file($authConfigPath);
        $this->authConfigPath = $authConfigPath;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getAuthConfig(): array
    {
        $data = file_get_contents($this->authConfigPath);
        Assertion::isJsonString($data);
        $data = json_decode($data, true);

        return $data;
    }
}
