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

        static::updateDockerYaml();
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
            // Get data base credentials from local config file
            if (file_exists('./config/config.local.php')) {
                require './config/config.local.php';
            } else {
                self::printOutput("No config/config.local.php file found.", 'error');
            }

            // Make sure we have details to connect to the DB
            if (
                empty($config['database']['host']) ||
                empty($config['database']['dbname']) ||
                empty($config['database']['username']) ||
                empty($config['database']['password'])
            ) {
                self::printOutput("Database configuration values are not set in config/config.local.php.", 'error');
            }

            // Get the pitoncms/engine version from composer.lock file, this is the stored token each page view will check
            if (null === $definition = json_decode(file_get_contents('./composer.lock'))) {
                self::printOutput("Unable to read PitonCMS/Engine version from composer.lock.", 'error');
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

            // Update engine version
            $updateEngineSetting = 'update `data_store` set `setting_value` = ?, `updated_date` = ? where `category` = \'piton\' and `setting_key` = \'engine\';';
            $settingValue[] = $engineVersion;
            $settingValue[] = date('Y-m-d H:i:s');

            $stmt = $pdo->prepare($updateEngineSetting);
            $stmt->execute($settingValue);

            self::printOutput("Updated PitonCMS Engine version setting to $engineVersion.");
        } catch (Throwable $e) {
            if ($e->getCode() === 2002) {
                self::printOutput("Execute composer update inside docker container. Database server must be running to update: {$e->getMessage()}.", 'error');
            } else {
                self::printOutput("Failed to update engine setting: {$e->getMessage()}.", 'error');
            }
        }
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

        // Update docblock
        $lines[2] = ' * Local Environment Configuration Settings' . PHP_EOL;
        $lines[4] = ' * Define environment specific settings in this file.' . PHP_EOL;
        $lines[5] = ' * DO NOT commit to VCS!' . PHP_EOL;

        if ($lines) {
            foreach ($lines as &$line) {
                if (strpos($line, 'site') !== false && strpos($line, 'production') !== false) {
                    $line = str_replace('true', 'false', $line);
                }

                if (strpos($line, 'database') !== false && strpos($line, 'host') !== false) {
                    $line = str_replace('localhost', 'db', $line);
                }

                if (strpos($line, 'database') !== false && strpos($line, 'dbname') !== false) {
                    $line = str_replace('\'\'', '\'' . $projectDir . '\'', $line);
                }

                if (strpos($line, 'database') !== false && strpos($line, 'username') !== false) {
                    $line = str_replace('\'\'', '\'' . $projectDir . '\'', $line);
                }

                if (strpos($line, 'database') !== false && strpos($line, 'password') !== false) {
                    $line = str_replace('\'\'', '\'' . $projectDir . '\'', $line);
                }

                if (strpos($line, 'session') !== false && strpos($line, 'cookieName') !== false) {
                    $line = str_replace('\'\'', '\'' . $projectDir . '\'', $line);
                }

                if (strpos($line, 'session') !== false && strpos($line, 'salt') !== false) {
                    $line = str_replace('\'\'', '\'' . $salt . '\'', $line);
                }
            }

            static::printOutput("> If using SMTP email set credentials in config.local.php.", 'info');

            file_put_contents('config/config.local.php', $lines);
        } else {
            static::printOutput("Unable to read config/config.default.php to copy.", 'error');
            static::printOutput("Copy config/config.default.php to config/config.local.php and edit configuration settings", 'error');
        }
    }

    /**
     * Update Docker Yaml
     *
     * Update docker-compose.yml file with project name.
     * @param  void
     * @return void
     */
    protected static function updateDockerYaml()
    {
        static::printOutput("...Updating docker-compose.yml for project");

        $projectDir = self::getProjectDir();
        $lines = file('docker-compose.yml');

        if ($lines) {
            foreach ($lines as &$line) {
                if (strpos($line, 'image') !== false) {
                    $line = str_replace('piton', $projectDir, $line);
                }

                if (strpos($line, '/var/www/') !== false) {
                    $line = str_replace('piton', $projectDir, $line);
                }

                if (strpos($line, 'mysql-data') !== false) {
                    $line = str_replace('piton', $projectDir, $line);
                }

                if (strpos($line, 'MYSQL_DATABASE') !== false) {
                    $line = str_replace('piton', $projectDir, $line);
                }

                if (strpos($line, 'MYSQL_USER') !== false) {
                    $line = str_replace('piton', $projectDir, $line);
                }

                if (strpos($line, 'MYSQL_PASSWORD') !== false) {
                    $line = str_replace('piton', $projectDir, $line);
                }
            }

            file_put_contents('docker-compose.yml', $lines);
        } else {
            static::printOutput("Unable to read docker-compose.yml. Update manually to use docker-compose.", "error");
        }
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
        static::printOutput("...Writing apache host file for project");

        $projectDir = self::getProjectDir();
        $lines = file('docker/web/apache-host.conf');

        if ($lines) {
            foreach ($lines as &$line) {
                if (strpos($line, 'DocumentRoot') !== false) {
                    $line = str_replace('piton', $projectDir, $line);
                }

                if (strpos($line, '<Directory') !== false) {
                    $line = str_replace('piton', $projectDir, $line);
                }
            }

            file_put_contents('docker/web/apache-host.conf', $lines);
        } else {
            static::printOutput("Unable to read docker/web/apache-host.conf. Update manually to use docker-compose.", "error");
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
     * Print Output
     *
     * @param string $string
     * @param string $type status|info|error
     * @return void
     */
    protected static function printOutput(string $string, $type = 'status')
    {
        if ($type === 'status') {
            echo "\033[0;32m$string\033[0m\n";
        } elseif ($type === 'info') {
            echo "\033[43m$string\033[0m\n";
        } elseif ($type === 'error') {
            echo "\033[1;37m\033[41mError: $string\033[0m\n";
            exit;
        }
    }
}
