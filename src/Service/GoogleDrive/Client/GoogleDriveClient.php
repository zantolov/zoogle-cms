<?php

declare(strict_types=1);

namespace Zantolov\Zoogle\Cms\Service\GoogleDrive\Client;

use Google\Service\Docs\Document;
use Google\Service\Drive\DriveFile;

interface GoogleDriveClient
{
    /** @return DriveFile[] */
    public function listDirectories(string $directoryId = null, int $limit = 1000): array;

    /** @return DriveFile[] */
    public function listRootDirectories(int $limit = 1000): array;

    /** @return DriveFile[] */
    public function listDocs(string $directoryId, int $limit = 1000): array;

    /** @return DriveFile[] */
    public function listAllDocs(int $limit = 1000): array;

    /** @return DriveFile[] */
    public function searchDocs(string $query, int $limit = 1000): array;

    public function getDocAsHTML(string $fileId): string;

    public function getDoc(string $fileId): Document;

    /**
     * @return DriveFile<DriveFile>
     */
    public function getFile(string $fileId): DriveFile;

    /**
     * @return null|DriveFile<DriveFile>
     */
    public function findByName(string $name): ?DriveFile;
}
