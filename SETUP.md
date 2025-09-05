# Task Manager with Google Drive & Gemini AI Integration

## Features
- Create and manage tasks with priority levels
- Upload documents (PDF, DOC, DOCX, images) to Google Drive
- AI-powered task analysis using Gemini API
- Auto-generated task descriptions
- Document management with Google Drive integration

## Setup Instructions

### 1. Install Dependencies
```bash
composer install
```

### 2. Database Setup
```bash
php artisan migrate
```

### 3. Google Drive API Setup
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing one
3. Enable Google Drive API
4. Create a Service Account
5. Download the JSON credentials file
6. Rename it to `google-credentials.json` and place in `storage/app/`

### 4. Gemini API Setup
1. Go to [Google AI Studio](https://makersuite.google.com/app/apikey)
2. Create an API key
3. Add it to your `.env` file:
```
GEMINI_API_KEY=your_gemini_api_key_here
```

### 5. File Permissions
Make sure the storage directory is writable:
```bash
chmod -R 775 storage/
```

### 6. Run the Application
```bash
php artisan serve
```

## Usage

### Creating Tasks
1. Navigate to `/tasks/create`
2. Fill in task details
3. Upload documents (optional)
4. Submit - AI will analyze and generate descriptions

### Managing Tasks
- View all tasks on the dashboard
- Edit task status and details
- View attached documents stored in Google Drive
- Delete tasks (removes Google Drive files too)

## API Integrations

### Google Drive API
- Uploads files to Google Drive
- Creates public view links
- Manages file permissions
- Deletes files when tasks are removed

### Gemini AI API
- Analyzes task complexity and requirements
- Generates detailed task descriptions
- Provides insights and suggestions

## File Structure
```
app/
├── Http/Controllers/
│   └── TaskController.php
├── Models/
│   ├── Task.php
│   └── Document.php
└── Services/
    ├── GoogleDriveService.php
    └── GeminiService.php

resources/views/
├── layouts/
│   └── app.blade.php
└── tasks/
    ├── index.blade.php
    ├── create.blade.php
    ├── show.blade.php
    └── edit.blade.php
```

## Security Notes
- Keep your Google credentials file secure
- Never commit API keys to version control
- Use environment variables for sensitive data
- Implement proper authentication in production