<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2026 Wolfgang Moritz
 * @license   AGPL-3.0-or-later with Theme Exception. See LICENSE file for details.
 */

declare(strict_types=1);

namespace Piton\Library\Utilities;

use Exception;
use PDO;
use PDOException;

/**
 * Installer Check
 *
 * Checks if Piton database tables have been created and setup records exist
 * Leaves a piton-installed.lock sentinel file
 *
 * Logic flow:
 * - Checks for a sentinel lock file
 *      - If a sentinel lock file is found then return and stop
 * - Try a simple query for an admin user
 *      - If a table not found exception is thrown then create schema and start over
 *      - If the query ran but no admin user found, then present the setup form (except on the saveSetup POST route)
 *      - If the query ran and an admin user was found, then create the sentinel lock file
 */
class Installer
{
    protected static PDO $pdo;
    protected static string $installedLockFile = ROOT_DIR . 'piton-installed.lock';
    protected static string $testQuery = "select 1 from `user` where `role` = 'A' and `active` = 'Y' limit 1;";
    protected static string $schemaBuildScript = ROOT_DIR . 'vendor/pitoncms/engine/schema/build.sql';
    protected static string $setupForm = ROOT_DIR . 'vendor/pitoncms/engine/templates/system/setup.html';

    /**
     * Run
     *
     * @param PDO $pdo Database connection
     * @return void
     * @throws Exception
     */
    public static function run(PDO $pdo)
    {
        self::$pdo = $pdo;

        // Check if installed.lock file exits
        if (file_exists(self::$installedLockFile)) {
            // Good, Piton DB installed and nothing to do
            return;
        }

        // Otherwise, if no lock file then...
        try {
            // Try a simple query to test if the schema and if the setup information has been saved.
            $result = self::$pdo->query(self::$testQuery);
            $row = $result->fetch();

            if ($row) {
                // An admin user was found which implies both schema and setup information present. Create lock file and return
                file_put_contents(self::$installedLockFile, 'Indicator that database has been installed');

                return;
            }

            // Otherwise, schema exists but no admin user was found. Display setup form and exit.
            // Ignore the 'saveSetup' POST route used by the setup form
            $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            if ($path !== 'saveSetup' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
                self::showSetupForm();

                exit;
            }

        } catch (PDOException $e) {
            // If we are here no table was found, so build schema
            if ($e->getCode() === '42S02') {
                // Create schema
                self::buildSchema();

                // Now that we have a schema, run again to load setup form
                return self::run(self::$pdo);
            } else {
                // Edge case, re-raise exception
                throw $e;
            }
        }
    }

    /**
     * Create Schema
     *
     * Runs SQL script to build schema
     * @param void
     * @return void
     */
    public static function buildSchema(): void
    {
        self::$pdo->exec(file_get_contents(self::$schemaBuildScript));
    }

    public static function showSetupForm()
    {
        echo file_get_contents(self::$setupForm);
    }

    /**
     * Get Lock File Name
     *
     * @param void
     * @return string
     */
    public static function getLockFileName(): string
    {
        return self::$installedLockFile;
    }
}
