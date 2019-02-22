<?php
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */
namespace Piton\Library\Utilities;

use Composer\Installer\PackageEvent;

/**
 * Piton Installer Scripts
 */
class PitonInstall
{
    /**
     * Update Docker Yaml
     *
     * Update docker-compose.yml directory paths using project name
     * @param  object $event Composer\Script\Event
     * @return void
     */
    public static function updateDockerYaml(PackageEvent $event)
    {
        // Get project directory name
        $package = $event->getOperation()->getPackage();
        $installationManager = $event->getComposer()->getInstallationManager();
        $projectDir = $installationManager->getInstallPath($package);

        $content = <<<TEXT
version: '3'

services:
  web:
    build: ./docker/web
    image: {$projectDir}
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - .:/var/www/{$projectDir}
    depends_on:
      - db
    tty: true

  db:
    image: mariadb:10.3.7
    ports:
      - "3306:3306"
    volumes:
      - ~/.mysql-data/{$projectDir}:/var/lib/mysql
    environment:
      - MYSQL_ROOT_PASSWORD=rootpassword
      - MYSQL_DATABASE={$projectDir}
      - MYSQL_USER={$projectDir}
      - MYSQL_PASSWORD={$projectDir}password

TEXT;

        file_put_contents('docker-compose.yml', $content);
    }

    /**
     * Update Apache Host
     *
     * Update docker/web/apache-host.conf directory paths using project name
     * @param  object $event Composer\Script\Event
     * @return void
     */
    public static function updateApacheHost(PackageEvent $event)
    {
        // Get project directory name
        $package = $event->getOperation()->getPackage();
        $installationManager = $event->getComposer()->getInstallationManager();
        $projectDir = $installationManager->getInstallPath($package);

        $content = <<<TEXT
<VirtualHost *:80>

  ServerAdmin webmaster@localhost
  DocumentRoot /var/www/{$projectDir}/public/

  <Directory /var/www/{$projectDir}/public>
    Options FollowSymLinks
    AllowOverride All
    Require all granted
  </Directory>

</VirtualHost>

TEXT;

        file_put_contents('docker/web/apache-host.conf', $content);
    }
}
