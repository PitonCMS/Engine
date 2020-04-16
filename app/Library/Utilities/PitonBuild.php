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
use PDO;
use Throwable;

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
    public static function createProject(Event $event): void
    {
        static::printOutput("...Completing new project setup");

        static::updateDockerYaml($event);
        static::updateApacheHost();
        static::copyConfig();

        static::printOutput('> To start Docker, from the root of this project first run \'docker-compose build\' to create the image, a one-time step.', 'info');
        static::printOutput('> Then run \'docker-compose up -d\' and navigate to http://localhost to finish the installation.', 'info');
    }

    /**
     * Piton Update Engine
     *
     * Called after running composer update on pitoncms/engine.
     * Updates the engine setting value to the current build.
     * @param Event $event
     * @return void
     */
    public static function updateEngine(Event $event): void
    {
        try {
            // Get data base credentials from config file
            if (file_exists('./config/config.local.php')) {
                require './config/config.local.php';
            } else {
                self::printOutput("No config/config.local.php file found.", 'exception');
            }

            // Make sure we have details to connect to the DB
            if (
            (!isset($config['database']['host']) || empty($config['database']['host'])) ||
            (!isset($config['database']['dbname']) || empty($config['database']['dbname'])) ||
            (!isset($config['database']['username']) || empty($config['database']['username'])) ||
            (!isset($config['database']['password']) || empty($config['database']['password']))
            ) {
                self::printOutput("Configuration database values are not all set in config/config.local.php. Edit config/config.local.php.", 'exception');
            }

            // Get the pitoncms/engine version from composer.lock file, this is the stored token each page view will check
            if (null === $definition = json_decode(file_get_contents('./composer.lock'))) {
                self::printOutput("Unable to read PitonCMS/Engine version from composer.lock.", 'exception');
            }
            $engineKey = array_search('pitoncms/engine', array_column($definition->packages, 'name'));
            $engineVersion = $definition->packages[$engineKey]->version;

            // Setup database config
            $dbConfig = $config['database'];
            $dbConfig['options'][PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
            $dbConfig['options'][PDO::ATTR_EMULATE_PREPARES] = false;

            // Define connection string
            $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};charset=utf8mb4";

            // Return connection
            $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $dbConfig['options']);

            // Insert engine version as key to avoid running install again
            $updateEngineSetting = 'update `data_store` set `setting_value` = ?, `updated_date` = ? where `category` = \'piton\' and `setting_key` = \'engine\';';
            $settingValue[] = $engineVersion;
            $settingValue[] = date('Y-m-d H:i:s');


            $stmt = $pdo->prepare($updateEngineSetting);
            $stmt->execute($settingValue);

            self::printOutput("Updated PitonCMS Engine version setting.");
        } catch (Throwable $e) {
            self::printOutput("Failed to update engine setting: {$e->getMessage()}.", 'exception');
        }
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
        static::printOutput("...Creating config file for local development");

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

            static::printOutput("...If using SMTP email set credentials in config.local.php. Otherwise delete email config block to use default 'mail' protocol", 'info');

            file_put_contents('config/config.local.php', $lines);
        } else {
            static::printOutput("...Copy config/config.default.php to config/config.local.php and edit configuration settings");
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
        static::printOutput("...Updating docker-compose with project directory");

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
        static::printOutput("...Updating apache host file");

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

    /**
     * Print Output
     *
     * @param string $string
     * @param string $type status|info|exception
     * @return void
     */
    protected static function printOutput(string $string, $type = 'status')
    {
        if ($type === 'status') {
            echo "\033[0;32m$string\033[0m\n";
        } elseif ($type === 'info') {
            echo "\033[43m$string\033[0m\n";
        } elseif ($type === 'exception') {
            echo "\033[1;37m\033[41mException: $string\033[0m\n";
            exit;
        }
    }
}
