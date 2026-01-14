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
 * Checks if Piton database tables have been created.
 * Leaves a piton-installed.lock sentinel file
 */
class Installer
{
    protected static PDO $pdo;
    protected static string $installedLockFile = ROOT_DIR . 'piton-installed.lock';
    protected static string $installerScript = ROOT_DIR . 'public/install.php';
    protected static string $testQuery = 'select 1 from `user` where id = 1;';

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
            // Good, Piton app installed and nothing to do
            return;
        }

        try {
            self::$pdo->query(self::$testQuery);
        } catch (PDOException $e) {
            if (file_exists(self::$installerScript)) {
                header('Location: /install.php', true, 302);
                exit;
            } else {
                // Edge case, re-raise exception
                throw $e;
            }
        }

        // Create lock file
        file_put_contents(self::$installedLockFile, 'Indicator that database has been installed');

        return;
    }
}
