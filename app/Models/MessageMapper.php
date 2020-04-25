<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

namespace Piton\Models;

use Piton\ORM\DataMapperAbstract;

/**
 * Piton Message Mapper
 */
class MessageMapper extends DataMapperAbstract
{
    protected $table = 'message';
    protected $modifiableColumns = [
        'name',
        'email',
        'message',
        'is_read',
        'context'
    ];

    /**
     * Find Messages in Date Order
     *
     * @param void
     * @return array|null
     */
    public function findAllInDateOrder(): ?array
    {
        $this->makeSelect();
        $this->sql .= ' order by created_date desc';

        return $this->find();
    }

    /**
     * Find Unread Count
     *
     * Gets the count of unread messages
     * @param  void
     * @return int
     */
    public function findUnreadCount(): int
    {
        $this->sql = 'select count(*) unread from message where is_read = \'N\'';

        return (int) $this->findRow()->unread;
    }

    /**
     * Find Unread Messages
     *
     * Gets all unread messages
     * @param  void
     * @return array|null
     */
    public function findUnread(): ?array
    {
        $this->makeSelect();
        $this->sql .= 'and is_read = \'N\'';

        return $this->find();
    }

    /**
     * Mark As Read
     *
     * @param  int  $messageId
     * @return bool
     */
    public function markAsRead(int $messageId): bool
    {
        $this->sql = 'update message set is_read = \'Y\' where id = ?';
        $this->bindValues[] = $messageId;

        return $this->execute();
    }

    /**
     * Mark As Unread
     *
     * @param  int  $messageId
     * @return bool
     */
    public function markAsUnread(int $messageId): bool
    {
        $this->sql = 'update message set is_read = \'N\' where id = ?';
        $this->bindValues[] = $messageId;

        return $this->execute();
    }
}
