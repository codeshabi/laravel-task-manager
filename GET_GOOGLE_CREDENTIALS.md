# Get Real Google Drive Credentials

## Step 1: Go to Google Cloud Console
1. Visit: https://console.cloud.google.com/
2. Select project: `gen-lang-client-0899226688`

## Step 2: Enable Google Drive API
1. Go to "APIs & Services" > "Library"
2. Search "Google Drive API"
3. Click "Enable"

## Step 3: Create Service Account
1. Go to "APIs & Services" > "Credentials"
2. Click "Create Credentials" > "Service Account"
3. Name: `taskmanager-service`
4. Click "Create and Continue"
5. Skip roles, click "Continue" then "Done"

## Step 4: Generate Key
1. Click on the service account you created
2. Go to "Keys" tab
3. Click "Add Key" > "Create New Key"
4. Select "JSON"
5. Download the file

## Step 5: Replace Credentials
1. Replace `storage/app/google-credentials.json` with the downloaded file
2. Your documents will now upload to Google Drive in a "tasks" folder

## Current Status:
- ✅ Code is ready for Google Drive uploads
- ✅ Will create "tasks" folder automatically
- ❌ Need real credentials to work

Replace the credentials file and your uploads will work!