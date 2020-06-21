<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2020 Wolfgang Moritz
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
     * @param  string     $filter
     * @param  int        $limit
     * @param  int        $offset
     * @return array|null
     */
    public function findMessages(string $filter = 'read', int $limit = null, int $offset = null): ?array
    {
        $this->makeSelect(true);

        if ($filter === 'readUnRead') {
            $this->sql .= " and is_read in ('Y','N')";
        } elseif ($filter === 'read') {
            $this->sql .= " and is_read = 'Y'";
        } elseif ($filter === 'unread') {
            $this->sql .= " and is_read = 'N'";
        } elseif ($filter === 'archive') {
            $this->sql .= " and is_read = 'A'";
        }

        $this->sql .= ' order by created_date desc';

        if ($limit) {
            $this->sql .= ' limit ?';
            $this->bindValues[] = $limit;
        }

        if ($offset) {
            $this->sql .= ' offset ?';
            $this->bindValues[] = $offset;
        }

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

    /**
     * Mark As Archived
     *
     * @param  int  $messageId
     * @return bool
     */
    public function markAsArchived(int $messageId): bool
    {
        $this->sql = 'update message set is_read = \'A\' where id = ?';
        $this->bindValues[] = $messageId;

        return $this->execute();
    }
}
