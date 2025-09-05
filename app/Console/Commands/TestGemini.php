<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GeminiService;

class TestGemini extends Command
{
    protected $signature = 'test:gemini {title}';
    protected $description = 'Test Gemini API description generation';

    public function handle()
    {
        $title = $this->argument('title');
        $gemini = new GeminiService();
        
        $this->info("Testing Gemini API with title: {$title}");
        
        $description = $gemini->generateDescription($title);
        
        $this->info("Generated description:");
        $this->line($description);
        
        return 0;
    }
}