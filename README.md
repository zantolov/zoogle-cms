# Zoogle CMS - Zoran's Google Docs CMS

This project allows you to use Google Docs and its folders and docs to manage blog and/or content portal.

The project provides models and services needed to:
- Authorize application for usage with Google Drive
- Query Google Drive for folders (categories) and docs (articles) 
- Fetch and process Google Docs and convert them to HTML
- Content processing (extracting meta data, removing extra HTML tags and attributes, ...) 


# Setup

## 1. Require Composer package
`composer require zantolov/zoogle-cms`

## 2. Enable Symfony bundle

Edit `config/bundles.php` and add:

```
Zantolov\ZoogleCms\Infrastructure\Symfony\ZoogleCmsBundle::class => ['all' => true]
```

## 3. Obtaing Google API credentials
- Go to https://console.developers.google.com/iam-admin/serviceaccounts
- Create project or select existing one
- Click on `Create service account`
- Add permission to this service account as `Owner`
- Create JSON key for the service account and download it to `var/` (it will be needed later) 
- Enable Google Drive API
- Enable Google Docs API

## 4. Set-up required environment variables

Create local, untracked `.env` instance (e.g. `.env.dev.local`) and fill in the variables:

```
GOOGLE_DRIVE_API_CLIENT_ID=YOUR CLIENT ID
GOOGLE_DRIVE_API_AUTH_FILE_PATH=/var/auth.json
GOOGLE_DRIVE_ROOT_DIRECTORY_ID=YOUR ROOT DIRECTORY ID
```

- `GOOGLE_DRIVE_API_CLIENT_ID` The `client_id` value from the authorization JSON file you have downloaded
- `GOOGLE_DRIVE_API_AUTH_FILE_PATH` - path to the JSON credentials file downloaded while creating the service account
- `GOOGLE_DRIVE_ROOT_DIRECTORY_ID` - ID of the Google Drive folder that will be root for the CMS

### How to get the root directory ID?

- Create a folder on your Google Drive and open it with the browser
- See your URL in the following format: `https://drive.google.com/drive/u/0/folders/1123456AbCDeFGhIjKlMnB12233`
- The last URL component is the folder ID, `https://drive.google.com/drive/u/0/folders/{id}`

## 5. Grant access on the Google Drive root folder to the service account email

- Right click on the root folder in your Google drive and select "Share"
- Add the email contained in the JSON credentials file under the `client_email` key to the list of users the folder is shared with. View permission is enough
