<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2026 Wolfgang Moritz
 * @license   AGPL-3.0-or-later with Theme Exception. See LICENSE file for details.
 */

declare(strict_types=1);

namespace Piton\Controllers;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

/**
 * Piton Setup Controller
 *
 * Only used to save initial setup information once the DB schema was created by Piton\Library\Utilities\Installer
 */
class AdminSetupController extends AdminBaseController
{
    /**
     * Save Setup Data
     *
     * @param void
     * @uses POST data
     * @return Response
     */
    public function saveSetup(): Response
    {
        // Validate POST data
        $this->validateData();

        // Save data
        $this->saveInititalData();

        // Redirect home. First call of Installer::run in bootstrap will set sentinel file
        return $this->redirect('home');
    }

    /**
     * Validate POST Data
     *
     * @param void
     * @return void
     * @throws Exception
     */
    protected function validateData(): void
    {
        $required = ['firstName', 'lastName', 'email', 'siteName'];

        // Make sure all values were provided
        foreach ($required as $field) {
            if (empty($this->getParsedBodyParam($field))) {
                throw new Exception("PitonCMS: Missing required field: {$field}");
            }
        }

        // Check that email is valid and less than 255 characters
        $email = $this->getParsedBodyParam('email');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL, FILTER_FLAG_EMAIL_UNICODE) || strlen($email) > 255) {
            throw new Exception("PitonCMS: You must provide a valid email address less than 255 characters in length. {$email} is not valid.");
        }

        // Check that remaining values are less than 60 characters
        foreach (array_diff($required, ['email']) as $field) {
            if (strlen($this->getParsedBodyParam($field)) > 60) {
                throw new Exception("PitonCMS: Input {$field} is over the 60 character limit.");
            }
        }
    }

    /**
     * Insert Data
     *
     * Inserts initital site data
     * @param void
     * @return void
     * @throws Exception
     */
    protected function saveInititalData(): void
    {
        $PDO = $this->container->get('database');

        try {
            $PDO->beginTransaction();

            // Insert admin user
            $stmt = $PDO->prepare("
                insert into `user` (`first_name`, `last_name`, `email`, `role`, `created_date`, `updated_date`)
                values (:first_name, :last_name, :email, 'A', now(), now());
            ");
            $stmt->execute([
                'first_name' => $this->getParsedBodyParam('firstName'),
                'last_name' => $this->getParsedBodyParam('lastName'),
                'email' => $this->getParsedBodyParam('email'),
            ]);

            // Insert site name
            $stmt = $PDO->prepare("
                insert into `data_store` (`category`, `setting_key`, `setting_value`, `created_date`, `updated_date`)
                values ('site', 'siteName', :siteName, now(), now());
            ");
            $stmt->execute([
                'siteName' => $this->getParsedBodyParam('siteName'),
            ]);

            // Update engine version
            $stmt = $PDO->prepare("
                update `data_store` set `setting_value` = :setting_value, `updated_date` = now() where `category` = 'piton' and `setting_key` = 'engine';
             ");
            $stmt->execute([
                'setting_value' => $this->getEngineVersion(),
            ]);

            $PDO->commit();
        } catch (Throwable $th) {
            // Rollback and raise exception
            $PDO->rollBack();

            throw $th;
        }
    }

    /**
     * Get Engine Version
     *
     * Reads composer.lock file to get pitoncms/engine version
     * @param void
     * @return string
     * @throws Exception
     */
    private function getEngineVersion(): string
    {
        // Get the pitoncms/engine version from composer.lock file
        if (null === $definition = json_decode(file_get_contents(ROOT_DIR . 'composer.lock'))) {
            throw new Exception("PitonCMS: Unable to read composer.lock file to get pitoncms/engine version.");
        }

        $engineKey = array_search('pitoncms/engine', array_column($definition->packages, 'name'));

        return $definition->packages[$engineKey]->version ?? 'develop';
    }
}
