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
        
        // Handle credentials from environment variable
        if (env('GOOGLE_APPLICATION_CREDENTIALS_JSON')) {
            try {
                // Decode and validate the JSON credentials
                $credentials = json_decode(env('GOOGLE_APPLICATION_CREDENTIALS_JSON'), true, 512, JSON_THROW_ON_ERROR);
                
                // Create a temporary file with the properly formatted credentials
                $tempFile = tempnam(sys_get_temp_dir(), 'google_credentials_');
                file_put_contents($tempFile, json_encode($credentials, JSON_PRETTY_PRINT));
                
                $this->client->setAuthConfig($tempFile);
                unlink($tempFile); // Clean up the temporary file
            } catch (\Exception $e) {
                logger()->error('Failed to parse Google credentials: ' . $e->getMessage());
                throw new \RuntimeException('Invalid Google credentials configuration');
            }
        } else {
            // Fallback to file-based credentials
            $this->client->setAuthConfig(storage_path('keys/laravel-image-storage-c0626205a852.json'));
        }
        
        // Define explicit scopes needed for file operations
        $this->client->addScope([
            Google_Service_Drive::DRIVE_FILE,  // Per-file access
            Google_Service_Drive::DRIVE_METADATA_READONLY  // Read metadata
        ]);
        
        $this->client->setAccessType('offline');
        $this->client->setApplicationName(env('APP_NAME', 'TAJMS') . ' Profile Pictures');

        $this->driveService = new Google_Service_Drive($this->client);
        
        // Verify credentials and connection
        try {
            $this->driveService->files->listFiles(['pageSize' => 1]);
            logger()->info('Google Drive service account connection verified successfully');
        } catch (\Exception $e) {
            logger()->error('Google Drive service account verification failed: ' . $e->getMessage());
            logger()->error('Full error details: ' . json_encode([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString()
            ]));
            throw new \RuntimeException('Failed to initialize Google Drive service: ' . $e->getMessage());
        }
    }

    public function uploadFile($filePath, $mimeType, $originalName = null)
    {
        try {
            $filename = $originalName ?: uniqid('profile_', true);
    
            $fileMetadata = new Google_Service_Drive_DriveFile([
                'name' => $filename,
                'parents' => [env('GOOGLE_DRIVE_FOLDER_ID', '1jK02cuiyp3q93-A8aDqje7KIcWq7lebP')]
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
            logger()->error('Error details: ' . json_encode([
                'error_code' => $e->getCode(),
                'error_message' => $e->getMessage(),
                'error_errors' => $e->getErrors(),
                'file_path' => $filePath,
                'mime_type' => $mimeType,
                'original_name' => $originalName
            ]));
            return null;
        } catch (\Exception $e) {
            logger()->error('Unexpected error during Google Drive upload: ' . $e->getMessage());
            logger()->error('Stack trace: ' . $e->getTraceAsString());
            return null;
        }
    }
    
}