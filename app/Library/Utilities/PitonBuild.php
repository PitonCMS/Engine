<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

namespace Piton\Library\Utilities;

use Composer\Script\Event;

/**
 * Piton Build Scripts
 */
class PitonBuild
{

    /**
     * Piton Create Project
     *
     * Called after composer create-project
     * Updates docker build for current project directory
     * @param Event $event
     * @return void
     */
    public static function createProject(Event $event)
    {
        echo "...Completing new project setup\n";

        static::updateDockerYaml($event);
        static::updateApacheHost();
        static::copyConfig();
    }

    /**
     * Get Project Directory
     *
     */
    protected static function getProjectDir()
    {
        // This class is 6 levels deep from project root
        return basename(dirname(__DIR__, 6));
    }

    /**
     * Copy Config File
     *
     * @param void
     * @return void
     */
    protected static function copyConfig()
    {
        echo "...Creating config file for local development\n";

        $projectDir = self::getProjectDir();
        $salt = bin2hex(random_bytes(32));
        $lines = file('config/config.default.php');

        if ($lines) {
            foreach ($lines as &$line) {
                $line = str_replace('$config[\'site\'][\'production\'] = true;', '$config[\'site\'][\'production\'] = false;', $line);
                $line = str_replace('$config[\'database\'][\'host\'] = \'localhost\';', '$config[\'database\'][\'host\'] = \'db\';', $line);
                $line = str_replace('$config[\'database\'][\'dbname\'] = \'\';', '$config[\'database\'][\'dbname\'] = \'' . $projectDir . '\';', $line);
                $line = str_replace('$config[\'database\'][\'username\'] = \'\';', '$config[\'database\'][\'username\'] = \'' . $projectDir . '\';', $line);
                $line = str_replace('$config[\'database\'][\'password\'] = \'\';', '$config[\'database\'][\'password\'] = \'' . $projectDir . '\';', $line);
                $line = str_replace('$config[\'session\'][\'cookieName\'] = \'pitoncms\';', '$config[\'session\'][\'cookieName\'] = \'' . $projectDir . '\';', $line);
                $line = str_replace('$config[\'session\'][\'salt\'] = \'\';', '$config[\'session\'][\'salt\'] = \'' . $salt . '\';', $line);
            }

            echo "...If using SMTP email set credentials in config.local.php. Otherwise delete email config block to use 'mail' protocol\n";

            file_put_contents('config/config.local.php', $lines);
        } else {
            echo "...Copy config/config.default.php to config/config.local.php and edit configuration settings\n";
        }
    }


    /**
     * Update Docker Yaml
     *
     * Update docker-compose.yml directory paths using project name
     * @param  Event $event
     * @return void
     */
    protected static function updateDockerYaml(Event $event)
    {
        echo "...Updating docker-compose with project directory\n";

        $projectDir = self::getProjectDir();

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
    image: mysql:5.7
    ports:
      - "3306:3306"
    volumes:
      - ~/.mysql-data/{$projectDir}:/var/lib/mysql
    environment:
      - MYSQL_ROOT_PASSWORD=rootpassword
      - MYSQL_DATABASE={$projectDir}
      - MYSQL_USER={$projectDir}
      - MYSQL_PASSWORD={$projectDir}

TEXT;

        file_put_contents('docker-compose.yml', $content);
    }

    /**
     * Update Apache Host
     *
     * Update docker/web/apache-host.conf directory paths using project name
     * @param  void
     * @return void
     */
    protected static function updateApacheHost()
    {
        echo "...Updating apache host file\n";

        $projectDir = self::getProjectDir();

        $content = <<<TEXT
<VirtualHost *:80>

  ServerAdmin webmaster@localhost
  DocumentRoot /var/www/$projectDir/public/

  <Directory /var/www/$projectDir/public>
    Options FollowSymLinks
    AllowOverride All
    Require all granted
  </Directory>

</VirtualHost>

TEXT;

        file_put_contents('docker/web/apache-host.conf', $content);
    }
}
