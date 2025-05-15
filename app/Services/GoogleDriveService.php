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
    protected $initializationError;

    public function __construct()
    {
        try {
            $this->client = new Google_Client();
            
            // Handle credentials from environment variable
            if (env('GOOGLE_CLOUD_KEY_FILE')) {
                try {
                    // Log the first few characters of credentials for verification
                    $credentials = json_decode(env('GOOGLE_CLOUD_KEY_FILE'), true, 512, JSON_THROW_ON_ERROR);
                    logger()->info('Initializing Google Drive service with project: ' . ($credentials['project_id'] ?? 'unknown'));
                    
                    // Verify required fields
                    $requiredFields = ['type', 'project_id', 'private_key', 'client_email'];
                    foreach ($requiredFields as $field) {
                        if (empty($credentials[$field])) {
                            throw new \RuntimeException("Missing required field: {$field}");
                        }
                    }
                    
                    // Create a temporary file with the properly formatted credentials
                    $tempFile = tempnam(sys_get_temp_dir(), 'google_credentials_');
                    if ($tempFile === false) {
                        throw new \RuntimeException('Failed to create temporary file');
                    }
                    
                    // Ensure private key is properly formatted
                    if (!str_contains($credentials['private_key'], '-----BEGIN PRIVATE KEY-----')) {
                        throw new \RuntimeException('Invalid private key format');
                    }
                    
                    file_put_contents($tempFile, json_encode($credentials, JSON_PRETTY_PRINT));
                    
                    $this->client->setAuthConfig($tempFile);
                    unlink($tempFile); // Clean up the temporary file
                } catch (\Exception $e) {
                    logger()->error('Failed to parse Google credentials: ' . $e->getMessage());
                    logger()->error('Credentials validation error details: ' . json_encode([
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'credentials_start' => substr(env('GOOGLE_CLOUD_KEY_FILE'), 0, 50) . '...'
                    ]));
                    throw new \RuntimeException('Invalid Google credentials configuration: ' . $e->getMessage());
                }
            } else {
                // Fallback to file-based credentials
                $credentialsPath = storage_path('keys/laravel-image-storage-33b672fbc837.json');
                if (!file_exists($credentialsPath)) {
                    throw new \RuntimeException('Google credentials file not found: ' . $credentialsPath);
                }
                $this->client->setAuthConfig($credentialsPath);
            }
            
            // Define explicit scopes needed for file operations
            $this->client->addScope([
                Google_Service_Drive::DRIVE_FILE,
                Google_Service_Drive::DRIVE_METADATA_READONLY
            ]);
            
            $this->client->setAccessType('offline');
            $this->client->setApplicationName(env('APP_NAME', 'TAJMS') . ' Profile Pictures');

            $this->driveService = new Google_Service_Drive($this->client);
            
            // Verify credentials and connection
            try {
                $this->driveService->files->listFiles(['pageSize' => 1]);
                logger()->info('Google Drive service account connection verified successfully');
            } catch (\Exception $e) {
                logger()->error('Google Drive API test failed: ' . $e->getMessage());
                logger()->error('API test error details: ' . json_encode([
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'trace' => $e->getTraceAsString()
                ]));
                throw $e;
            }
        } catch (\Exception $e) {
            logger()->error('Google Drive service initialization failed: ' . $e->getMessage());
            // Don't throw here - allow the service to be created even if verification fails
            // This allows the application to continue running with degraded functionality
            $this->initializationError = $e->getMessage();
        }
    }

    public function uploadFile($filePath, $mimeType, $originalName = null)
    {
        try {
            // Check if service was properly initialized
            if (isset($this->initializationError)) {
                throw new \RuntimeException('Google Drive service not properly initialized: ' . $this->initializationError);
            }

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
        } catch (\Exception $e) {
            logger()->error('File upload failed: ' . $e->getMessage());
            logger()->error('Upload error details: ' . json_encode([
                'file_path' => $filePath,
                'mime_type' => $mimeType,
                'original_name' => $originalName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]));
            return null;
        }
    }
    
}