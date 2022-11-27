<?php

declare(strict_types=1);

namespace Zantolov\Zoogle\Cms\Service\GoogleDrive\Client;

use Assert\Assertion;

final class GoogleDriveAuth
{
    public function __construct(private string $clientId, private string $authConfigPath)
    {
        Assertion::file($this->authConfigPath);
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @throws \Assert\AssertionFailedException
     *
     * @return array<int, mixed>
     */
    public function getAuthConfig(): array
    {
        $data = \Safe\file_get_contents($this->authConfigPath);
        Assertion::isJsonString($data);

        return \Safe\json_decode($data, true);
    }
}
