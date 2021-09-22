# Overview

PitonCMS was designed to be a designer friendly CMS, giving great flexibility to the designer to build creative websites without requiring additional custom backend development.

Page structures, custom data, settings, are all easily extensible by modifying project JSON *Definition* files. These files can be checked into version control and pushed to other environments to promote layout and data changes without having to modify remote databases.

## Project Structure
When you install PitonCMS/Piton using Composer, a project directory is created containing the following structure

- `app/` For any extensions to customize core PitonCMS
- `cache/` Cached files. This directory can be safely emptied at any time, and should be emptied with each deployment
- `config/` This directory contains runtime configuration files
  - `config.local.php` Application configuration for the local environment, and overrides the default configuration
  - `dependencies.php.example` Overrides core PitonCMS Dependency Injection Container (DIC)
  - `routes.php.example` Overrides and extends routes
- `docker/` Docker configuration files if using Docker for local development
- `logs/` Contains application log files. The `logs/` directory can be safely emptied at any time
- `public/` The **public** folder is your web Document Root, and most files are accessible from the web
  - `admin/` Symbolic link to PitonCMS Administration assets (do not change)
  - `assets/` For frontend public CSS, JS, and static IMG assets
  - `media/` Holds uploaded media files
  - `.htaccess` Configure Apache runtime settings
  - `index.php` Entry point for all PitonCMS web requests. The `.htaccess` file rewrites web requests to use `index.php`
  - `install.php` Database installer script. This file self-deletes after first use, and should **NOT** be committed to version control
- `structure/` Contains all HTML and JSON Definition files for your website
- `vendor/` Contains project dependenices, managed by Composer
- `.gitignore` Modify to exclude files from Git version control
- `composer.json` Defines project dependencies.
- `composer.lock` Defines exact state of all dependencies
- `.htaccess` The **.htaccess** file at the project root simply denies web access above the public Document Root.
- `docker-compose.yml` Defines the Docker Compose development images. Also has your *development* database user and password. If you are not using Docker for development, delete the `docker-compose.yml` and the `docker/` folder from the project

>**Note**: When building PitonCMS with Composer using `create-project`, the default config file is copied to `config/config.local.php` and updated with project information. If you did not use composer, you need to copy `vendor/pitoncms/engine/config/config.default.php` to `config/config.local.php` and update any settings you wish to modify.

## JSON Definition Files
You can easily customize many aspects of PitonCMS by creating and editing JSON configuration files in the `structure/` directory. Be sure to commit these JSON files to version control. It may also be helpful to use an editor that supports JSON syntax to help validate your JSON.

PitonCMS relies on JSON *Definition* files to describe how *Page Templates* should work, define custom *Settings*, enable custom contact form inputs, and more. These JSON files are only read when creating or modifying content in the editor, not when the Page content is loaded by a visitor.

With these Definition files you can easily enable a custom input for nearly any data type that allows your client to easily edit and modify values without development support.

## Composer
All project dependencies are managed by [Composer](https://getcomposer.org/). PitonCMS is also built of separate Composer projects to allow easy upgrades.

Composer requires that you have Command Line Interface PHP available on your local development machine. However, because Composer checks the current environment to determine dependency version, be sure to run all updates from within your Docker or AMP development server - not your host machine terminal. Your Docker or AMP development environment may be running a different version of PHP, and should be close to your actual production environment.

## Docker
If you have Docker Desktop running on your development computer, you can use the prebuilt Docker image that comes with PitonCMS. See [PitonCMS Readme](https://github.com/PitonCMS/Piton) to get started with Docker.

>**Note**: This Docker image is **NOT** suitable for production use.

## Twig
PitonCMS uses Twig to render HTML templates. You can read more about [Twig](https://twig.symfony.com/).