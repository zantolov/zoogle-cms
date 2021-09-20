<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Service\GoogleDrive\Client;

use Assert\Assertion;

final class GoogleDriveAuth
{
    /** @var string */
    private $clientId;

    /** @var string */
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
