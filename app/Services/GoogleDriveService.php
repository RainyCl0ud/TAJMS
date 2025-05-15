<?php

namespace App\Services;

use Google\Client as Google_Client;
use Google\Service\Drive as Google_Service_Drive;
use Google\Service\Drive\DriveFile as Google_Service_Drive_DriveFile;
use Google\Service\Exception as Google_Service_Exception;

class GoogleDriveService
{
    protected $client;
    protected $driveService;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setAuthConfig(storage_path('keys/laravel-image-storage-c0626205a852.json')); // ✅ Update with actual filename
        $this->client->addScope(Google_Service_Drive::DRIVE);
        $this->client->setAccessType('offline');

        $this->driveService = new Google_Service_Drive($this->client);
    }

    public function uploadFile($filePath, $mimeType, $originalName = null)
    {
        try {
            $filename = $originalName ?: uniqid('profile_', true);
    
            $fileMetadata = new Google_Service_Drive_DriveFile([
                'name' => $filename,
                'parents' => ['1jK02cuiyp3q93-A8aDqje7KIcWq7lebP']
            ]);
    
            $file = $this->driveService->files->create($fileMetadata, [
                'data' => file_get_contents($filePath),
                'mimeType' => $mimeType,
                'uploadType' => 'multipart',
                'fields' => 'id'
            ]);
    
            // Make file public
            $permission = new \Google\Service\Drive\Permission();
            $permission->setType('anyone');
            $permission->setRole('reader');
            $this->driveService->permissions->create($file->id, $permission);
    
            logger()->info('Uploaded to Google Drive with ID: ' . $file->id);
    
            // Return a web-viewable URL instead of download URL
            return "https://drive.google.com/uc?export=view&id=" . $file->id;
        } catch (Google_Service_Exception $e) {
            logger()->error('Google Drive upload error: ' . $e->getMessage());
            return null;
        }
    }
    
}