<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class GoogleDriveService
{
    private $client;
    private $service;
    private $tasksFolderId;

    public function __construct()
    {
        if (!env('GOOGLE_DRIVE_ENABLED', false)) {
            throw new \Exception('Google Drive is disabled');
        }

        $this->client = new Client();
        
        $credentialsPath = storage_path('app/google-credentials.json');
        if (!file_exists($credentialsPath)) {
            throw new \Exception('Google credentials file not found');
        }
        
        $this->client->setAuthConfig($credentialsPath);
        $this->client->addScope([Drive::DRIVE_FILE]);
        $this->service = new Drive($this->client);
        
        $this->tasksFolderId = $this->getOrCreateTasksFolder();
    }

    private function getOrCreateTasksFolder(): string
    {
        try {
            // Search for existing tasks folder
            $response = $this->service->files->listFiles([
                'q' => "name='tasks' and mimeType='application/vnd.google-apps.folder' and trashed=false",
                'fields' => 'files(id, name)'
            ]);
            
            if (count($response->files) > 0) {
                return $response->files[0]->id;
            }
            
            // Create tasks folder
            $folderMetadata = new \Google\Service\Drive\DriveFile([
                'name' => 'tasks',
                'mimeType' => 'application/vnd.google-apps.folder'
            ]);
            
            $folder = $this->service->files->create($folderMetadata, ['fields' => 'id']);
            Log::info('Created tasks folder with ID: ' . $folder->id);
            
            return $folder->id;
        } catch (\Exception $e) {
            Log::error('Failed to create tasks folder: ' . $e->getMessage());
            throw $e;
        }
    }

    public function uploadFile(UploadedFile $file, string $folderId = null): array
    {
        try {
            Log::info('Starting Google Drive upload for: ' . $file->getClientOriginalName());
            
            // Use tasks folder if no specific folder provided
            $parentFolderId = $folderId ?: $this->tasksFolderId;
            
            $fileMetadata = new \Google\Service\Drive\DriveFile([
                'name' => $file->getClientOriginalName(),
                'parents' => [$parentFolderId]
            ]);

            $content = file_get_contents($file->getPathname());
            
            $uploadedFile = $this->service->files->create($fileMetadata, [
                'data' => $content,
                'mimeType' => $file->getMimeType(),
                'uploadType' => 'multipart',
                'fields' => 'id'
            ]);

            Log::info('File uploaded to Google Drive tasks folder with ID: ' . $uploadedFile->id);

            // Make file publicly readable
            try {
                $permission = new \Google\Service\Drive\Permission([
                    'role' => 'reader',
                    'type' => 'anyone'
                ]);
                $this->service->permissions->create($uploadedFile->id, $permission);
                Log::info('File permissions set for: ' . $uploadedFile->id);
            } catch (\Exception $e) {
                Log::warning('Failed to set permissions: ' . $e->getMessage());
            }

            return [
                'id' => $uploadedFile->id,
                'url' => "https://drive.google.com/file/d/{$uploadedFile->id}/view"
            ];
        } catch (\Exception $e) {
            Log::error('Google Drive upload failed: ' . $e->getMessage());
            throw new \Exception('File upload failed: ' . $e->getMessage());
        }
    }

    public function deleteFile(string $fileId): bool
    {
        try {
            $this->service->files->delete($fileId);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}