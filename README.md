# FusionCMS Downloader

## Installation

### 1. Install Globally
Use Composer to install the FusionCMS downloader globally:

```
composer global require efellemedia/fusioncms-downloader
```

### 2. Register Token
After purchasing a FusionCMS license, run the `fusion register` command with your registration token

```
fusion register token-value
```

### 3. Download FusionCMS!
Once your FusionCMS client has been registered, you can run the `new` command to create new projects:

```
fusion new project-name
```

#### Specific Release
You may specify the release you wish to download in the instance you need an older version of the CMS through the second argument:

```
fusion new project-name v5.3.10
```
