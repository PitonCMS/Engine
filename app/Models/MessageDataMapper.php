<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2026 Wolfgang Moritz
 * @license   AGPL-3.0-or-later with Theme Exception. See LICENSE file for details.
 */

declare(strict_types=1);

namespace Piton\Models;

use Piton\ORM\DataMapperAbstract;

/**
 * Piton Message Data Mapper
 */
class MessageDataMapper extends DataMapperAbstract
{
    protected string $table = 'message_data';
    protected array $modifiableColumns = [
        'message_id',
        'data_key',
        'data_value',
    ];
    protected string $domainValueObjectClass = __NAMESPACE__ . '\Entities\MessageData';

    /**
     * Find Message Data
     *
     * @param int $messageId Message ID
     * @return ?array
     */
    public function findMessageDataByMessageId(int $messageId): ?array
    {
        $this->makeSelect();
        $this->sql .= " and `message_id` = ?";
        $this->bindValues[] = $messageId;

        return $this->find();
    }
}
