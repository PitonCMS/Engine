# PitonCMS for Designers

PitonCMS was designed to be _Designer Forward_, giving great flexibility to the designer to build creative websites without requiring additional custom backend development.

Page structures, custom data, settings, are all easily extensible by modifying project JSON **Definition** files. These files can be checked into version control and pushed to other environments to promote layout and data changes without having to modify remote databases or push code.

## Site Structure
When you install PitonCMS / Piton from composer, a project directory is created with these folders.

**`app/`**
For any custom code extensions to PitonCMS.

**`cache/`**
Holds cached files for Twig, the router, and any other cached content. The `cache/` directory can be safely emptied at any time, and should be emptied in production with each deployment.

**`config/`**
The **config** folder contains important site configuration files that are set as part of the site creation.
* `config/config.default.php` Default site settings. **DO NOT** change settings in this file. Copy any setting you wish to modify into `config/config.local.php`.
* `config/config.local.php` Site settings for the local environment, and overrides default settings. Copy any default settings you wish to modify into this file to modify.
* `config/dependencies.php.example` Overrides core PitonCMS Dependency Injection Container (DIC). Use if you want to replace a project dependency. Rename to `dependencies.php` to use.
* `config/routes.php.example` Overrides and extends front end routes. Rename to `routes.php` to use.

**`docker/`** Had Docker image configuration files.

**`logs/`** Contains log files, by date. The `logs/` directory can be safely emptied at any time.

**`public/`** The **public** folder is your web Document Root, and is accessible from the web at the root of your domain.
* `public/admin/` Links to PitonCMS Administration assets.
* `public/assets/` For frontend public CSS, JS, and static IMG assets.
* `public/media/` Stores uploaded media files.
* `public/.htaccess` Configure custom Apache runtime settings here.
* `public/index.php` Entry point for all PitonCMS web requests. The `.htaccess` file rewrites web requests to use `index.php`.
* `public/install.php` Database installer script. This file self-deletes after first use, and should **not** be committed to version control.

**`structure/`** Contains all HTML and JSON Definition files for your website.

**`vendor/`** Contains project dependenices, managed by Composer.

**`.gitignore`** Update to have git ignore any files from version control.

**`composer.json`** Defines project dependencies.

**`composer.lock`** Defines exact state of all dependencies, be sure to commit to your project version control.

**`.htaccess`** The **.htaccess** file at the project root simply denies web access above the public Document Root.

**`docker-compose.yml`** Defines the Docker Compose development image. Also has your development database user and password. If you are not using Docker for development, delete the `docker-compose.yml` and the `docker/` folder from the project.

## JSON Definition Files


## Composer


## Docker