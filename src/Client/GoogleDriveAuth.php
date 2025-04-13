<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Client;

use Assert\Assertion;

final readonly class GoogleDriveAuth
{
    private string $clientId;

    private string $authConfigPath;

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

    /**
     * @return array<string, string>
     */
    public function getAuthConfig(): array
    {
        $data = file_get_contents($this->authConfigPath);
        if ($data === false) {
            throw new \RuntimeException('Could not load the auth file');
        }

        Assertion::isJsonString($data);
        $data = json_decode($data, true);
        assert(is_array($data));

        /** @var array<string, string> $data */

        return $data;
    }
}
