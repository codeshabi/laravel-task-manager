<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleDriveService;
use Illuminate\Http\UploadedFile;

class TestUpload extends Command
{
    protected $signature = 'test:upload {file}';
    protected $description = 'Test file upload to Google Drive';

    public function handle()
    {
        $filePath = $this->argument('file');
        
        if (!file_exists($filePath)) {
            $this->error('File not found: ' . $filePath);
            return 1;
        }

        try {
            $drive = new GoogleDriveService();
            
            // Create a temporary uploaded file for testing
            $tempFile = tmpfile();
            $tempPath = stream_get_meta_data($tempFile)['uri'];
            copy($filePath, $tempPath);
            
            $uploadedFile = new UploadedFile(
                $tempPath,
                basename($filePath),
                mime_content_type($filePath),
                null,
                true
            );
            
            $result = $drive->uploadFile($uploadedFile);
            
            $this->info('File uploaded successfully!');
            $this->info('Google Drive ID: ' . $result['id']);
            $this->info('View URL: ' . $result['url']);
            
            fclose($tempFile);
            
        } catch (\Exception $e) {
            $this->error('Upload failed: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}