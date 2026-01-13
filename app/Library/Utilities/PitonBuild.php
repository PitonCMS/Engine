<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright 2019 - 2026 Wolfgang Moritz
 * @license   AGPL-3.0-or-later with Theme Exception. See LICENSE file for details.
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
    public static function createProject(Event $event): void
    {
        static::printOutput("...Completing new project setup");

        static::updateDockerYaml();
        static::updateApacheHost();
        static::copyConfig();
        static::createEnvironmentFile();

        static::printOutput('> To build the Docker image, from the root of this project run \'docker compose build\'.', 'info');
        static::printOutput('> To start Docker run \'docker compose up -d\' and navigate to http://localhost to finish the installation.', 'info');
    }

    /**
     * Piton Update Engine
     *
     * Called after running composer update on pitoncms/engine.
     * @param Event $event
     * @return void
     */
    public static function updateEngine(Event $event): void
    {
        // TODO
        static::printOutput("Update completed. It is best to run composer update from within the Docker container.");
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

        $projectName = self::getProjectDir();
        $salt = bin2hex(random_bytes(32));
        $lines = file('vendor/pitoncms/engine/config/config.default.php');

        // Update docblock
        $lines[2] = ' * Local Environment Configuration Settings' . PHP_EOL;
        $lines[4] = ' * Define environment specific settings in this file.' . PHP_EOL;
        $lines[5] = ' * DO NOT commit to VCS!' . PHP_EOL;

        // Set development configuration settings
        if ($lines) {
            foreach ($lines as &$line) {
                // Production environment to false
                if (strpos($line, 'environment') !== false && strpos($line, 'production') !== false) {
                    $line = str_replace('true', 'false', $line);
                }

                // Change localhost to Docker image 'db'
                if (strpos($line, 'database') !== false && strpos($line, 'host') !== false) {
                    $line = str_replace('\'\'', '\'db\'', $line);
                }

                // Change database name to project name
                if (strpos($line, 'database') !== false && strpos($line, 'dbname') !== false) {
                    $line = str_replace('\'\'', '\'' . $projectName . '\'', $line);
                }

                // Change database username to project name
                if (strpos($line, 'database') !== false && strpos($line, 'username') !== false) {
                    $line = str_replace('\'\'', '\'' . $projectName . '\'', $line);
                }

                // Change database password to project name
                if (strpos($line, 'database') !== false && strpos($line, 'password') !== false) {
                    $line = str_replace('\'\'', '\'' . $projectName . '\'', $line);
                }

                // Change session cookie name to project name
                if (strpos($line, 'session') !== false && strpos($line, 'cookieName') !== false) {
                    $line = str_replace('\'\'', '\'' . $projectName . 'session\'', $line);
                }

                // Set session salt to unique hash
                if (strpos($line, 'session') !== false && strpos($line, 'salt') !== false) {
                    $line = str_replace('\'\'', '\'' . $salt . '\'', $line);
                }

                // Set secure cookie to false
                if (strpos($line, 'session') !== false && strpos($line, 'secureCookie') !== false) {
                    $line = str_replace('true', 'false', $line);
                }
            }

            static::printOutput("> If using SMTP email set credentials in config.local.php.", 'info');

            file_put_contents('config/config.local.php', $lines);
        } else {
            static::printOutput("Unable to read vendor/pitoncms/engine/config/config.default.php.", 'error');
            static::printOutput("Copy vendor/pitoncms/engine/config/config.default.php to config/config.local.php and edit configuration settings", 'error');
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

        $projectName = self::getProjectDir();
        $lines = file('docker-compose.yml');

        if ($lines) {
            foreach ($lines as &$line) {
                if (strpos($line, '/var/www/') !== false) {
                    $line = str_replace('piton', $projectName, $line);
                }

                if (strpos($line, 'mysql-data') !== false) {
                    $line = str_replace('piton', $projectName, $line);
                }
            }

            file_put_contents('docker-compose.yml', $lines);
        } else {
            static::printOutput("Unable to read docker-compose.yml. Update manually to use docker compose.", "error");
        }
    }

    /**
     * Create Environment File (.env)
     *
     * For local development only.
     * @param  void
     * @return void
     */
    protected static function createEnvironmentFile()
    {
        static::printOutput("...Creating .env file for project");

        $projectName = self::getProjectDir();
        $environment = <<<ENV
        # Development local database credentials
        MYSQL_ROOT_PASSWORD=root
        MYSQL_DATABASE=$projectName
        MYSQL_USER=$projectName
        MYSQL_PASSWORD=$projectName
        ENV;

        file_put_contents('.env', $environment);
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

        $projectName = self::getProjectDir();
        $lines = file('docker/web/apache-host.conf');

        if ($lines) {
            foreach ($lines as &$line) {
                if (strpos($line, 'DocumentRoot') !== false) {
                    $line = str_replace('piton', $projectName, $line);
                }

                if (strpos($line, '<Directory') !== false) {
                    $line = str_replace('piton', $projectName, $line);
                }
            }

            file_put_contents('docker/web/apache-host.conf', $lines);
        } else {
            static::printOutput("Unable to read docker/web/apache-host.conf. Update manually to use docker compose.", "error");
        }
    }

    /**
     * Get Project Directory
     *
     */
    protected static function getProjectDir()
    {
        // This class is 6 levels deep from project root
        $dir = mb_strtolower(basename(dirname(__DIR__, 6)));

        // Strip dashes and spaces
        $dir = str_replace(['-', ' '], '', $dir);

        return $dir;
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
