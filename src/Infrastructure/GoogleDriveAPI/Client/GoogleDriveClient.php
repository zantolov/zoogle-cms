<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Infrastructure\GoogleDriveAPI\Client;


interface GoogleDriveClient
{
    /** @return \Google_Service_Drive_DriveFile[] */
    public function listDirectories(string $directoryId = null, int $limit = 1000): array;

    /** @return \Google_Service_Drive_DriveFile[] */
    public function listRootDirectories(int $limit = 1000): array;

    /** @return \Google_Service_Drive_DriveFile[] */
    public function listDocs(string $directoryId, int $limit = 1000): array;

    /** @return \Google_Service_Drive_DriveFile[] */
    public function listAllDocs(int $limit = 1000): array;

    public function getDocAsHTML(string $fileId): string;

    public function getDoc(string $fileId): \Google_Service_Docs_Document;

    public function getFile(string $fileId): \Google_Service_Drive_DriveFile;
}
