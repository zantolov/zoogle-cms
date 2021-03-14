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
ZOOGLE_CACHE=1
```

- `GOOGLE_DRIVE_API_CLIENT_ID` The `client_id` value from the authorization JSON file you have downloaded
- `GOOGLE_DRIVE_API_AUTH_FILE_PATH` - path to the JSON credentials file downloaded while creating the service account
- `GOOGLE_DRIVE_ROOT_DIRECTORY_ID` - ID of the Google Drive folder that will be root for the CMS
- `ZOOGLE_CACHE` - Configuration flag telling Zoogle to cache results or not

### Caching
Fetching and processing documents on every request is tedious job. Zoogle CMS provides cached
client that will fetch and process content only once, storing it locally for future access and 
faster loading time.

@todo cache invalidation

### How to get the root directory ID?

- Create a folder on your Google Drive and open it with the browser
- See your URL in the following format: `https://drive.google.com/drive/u/0/folders/1123456AbCDeFGhIjKlMnB12233`
- The last URL component is the folder ID, `https://drive.google.com/drive/u/0/folders/{id}`

## 5. Grant access on the Google Drive root folder to the service account email

- Right click on the root folder in your Google drive and select "Share"
- Add the email contained in the JSON credentials file under the `client_email` key to the list of users the folder is shared with. View permission is enough

## Usage

Once you've successfully shared Google Drive folder with the account associated to your Google project,
Zoogle CMS will have access to folders and documents under the selected root directory.

### Simple usage within Twig
```
{% set document = zoogle_document('https://docs.google.com/document/d/{some id}/edit') %}

<h1>{{ document.title }}</h1>

<div class="page__image">
    {{ document.firstImage|zoogle_element_html }}
</div>

{{ document.withoutFirstImage|zoogle_document_html }}
```

### Usage within PHP
#### Listing subfolders
#### Listing documents
#### Fetching document
##### Converting Google document to model object
##### Converting document model object to HTML

## Configurartion

### Images persistence
By default images exposed by Google Docs API will expire. To make sure images will keep rendering
on your website, we need to persist them.

Zoogle comes with the local persistence adapter powered by Symfony Cache component

To enable it, add the following route definition to your `app/config/routes.yaml`

```yaml
zoogle_cms_image:
    path: /z/image/{filename}
    controller: Zantolov\ZoogleCms\Infrastructure\Controller\ImageController
```

#### Amazon S3 images persistence
@todo

#### Custom persistence
@todo
