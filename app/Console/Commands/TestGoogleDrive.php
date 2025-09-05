<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleDriveService;

class TestGoogleDrive extends Command
{
    protected $signature = 'test:drive';
    protected $description = 'Test Google Drive connection';

    public function handle()
    {
        try {
            $drive = new GoogleDriveService();
            $this->info('Google Drive service initialized successfully!');
            
            // Test creating a simple text file
            $testContent = 'This is a test file from Laravel Task Manager';
            $tempFile = tempnam(sys_get_temp_dir(), 'test');
            file_put_contents($tempFile, $testContent);
            
            $this->info('Google Drive connection is working!');
            unlink($tempFile);
            
        } catch (\Exception $e) {
            $this->error('Google Drive test failed: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}