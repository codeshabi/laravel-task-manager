# Google Drive API Setup Guide

## Step 1: Create Service Account
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Select your project: `gen-lang-client-0899226688`
3. Enable Google Drive API:
   - Go to "APIs & Services" > "Library"
   - Search for "Google Drive API"
   - Click "Enable"

## Step 2: Create Service Account
1. Go to "APIs & Services" > "Credentials"
2. Click "Create Credentials" > "Service Account"
3. Fill in details:
   - Name: `taskmanager-service`
   - ID: `taskmanager-service`
   taskmanager-service@gen-lang-client-0899226688.iam.gserviceaccount.com
4. Click "Create and Continue"
5. Skip role assignment (click "Continue")
6. Click "Done"

## Step 3: Generate Key
1. Click on the created service account
2. Go to "Keys" tab
3. Click "Add Key" > "Create New Key"
4. Select "JSON" format
5. Download the file

## Step 4: Replace Credentials
1. Replace `storage/app/google-credentials.json` with downloaded file
2. Set `GOOGLE_DRIVE_ENABLED=true` in .env

## Step 5: Test
Run: `php artisan test:drive`