<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2026 Wolfgang Moritz
 * @license   AGPL-3.0-or-later with Theme Exception. See LICENSE file for details.
 */

declare(strict_types=1);

namespace Piton\Library\Handlers;

use Piton\Library\Interfaces\SessionInterface;
use Piton\Session\SessionHandler;

/**
 * Piton Session Class
 *
 * To use a different session manager class, implement Piton\Library\Interfaces\SessionInterface
 * and override the sessionHandler dependency in the container.
 */
class Session extends SessionHandler implements SessionInterface
{
    /**
     * Delete Session by User ID
     *
     * Searches session data to remove sessions for the specified user ID
     * @param int $userId
     * @return void
     */
    public function deleteSessionsByUserId(int $userId)
    {
        if ($this->log) {
            $this->log->info("Piton: Deleting sessions for user ID: $userId");
        }

        // Delete relevant sessions
        $user = '%"user_id":' . $userId . '%';
        $stmt = $this->db->prepare("DELETE FROM `{$this->tableName}` WHERE `data` LIKE ?;");
        $stmt->execute([$user]);
    }
}
