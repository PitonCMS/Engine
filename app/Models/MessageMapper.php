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
 * Piton Message Mapper
 */
class MessageMapper extends DataMapperAbstract
{
    protected string $table = 'message';
    protected array $modifiableColumns = [
        'name',
        'email',
        'message',
        'is_read',
        'context',
    ];
    protected string $domainValueObjectClass = __NAMESPACE__ . '\Entities\Message';

    /**
     * Find Messages in Date Order
     *
     * @param  string $filter
     * @param  ?int   $limit
     * @param  ?int   $offset
     * @return ?array
     */
    public function findMessages(string $filter = 'read', ?int $limit = null, ?int $offset = null): ?array
    {
        $this->makeSelect(true);

        if ($filter === 'readUnRead') {
            $this->sql .= " and `is_read` in ('Y','N')";
        } elseif ($filter === 'read') {
            $this->sql .= " and `is_read` = 'Y'";
        } elseif ($filter === 'unread') {
            $this->sql .= " and `is_read` = 'N'";
        } elseif ($filter === 'archive') {
            $this->sql .= " and `is_read` = 'A'";
        }

        $this->sql .= ' order by `created_date` desc';

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
     * Text Search
     *
     * This query searches each of these fields for having all supplied terms:
     *  - name
     *  - email
     *  - message
     *  - context
     *  - Custom message fields
     * @param  string $terms                Search terms
     * @param  ?int    $limit                Limit
     * @param  ?int    $offset               Offset
     * @return ?array
     */
    public function textSearch(string $terms, ?int $limit = null, ?int $offset = null): ?array
    {
        $this->makeSelect(true);
        $this->sql .= <<<SQL
and (
    match(`name`, `email`, `message`, `context`) against(? IN NATURAL LANGUAGE MODE)
    or `id` in (select `message_id` from `message_data` where match(`data_value`) against(? IN NATURAL LANGUAGE MODE))
    )
order by `created_date` desc
SQL;

        $this->bindValues[] = $terms;
        $this->bindValues[] = $terms;

        if ($limit) {
            $this->sql .= " limit ?";
            $this->bindValues[] = $limit;
        }

        if ($offset) {
            $this->sql .= " offset ?";
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
        $this->sql = "select count(*) unread from {$this->table} where `is_read` = 'N';";

        return (int) $this->findRow()->unread;
    }
}
