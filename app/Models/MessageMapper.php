<?php
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */
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
        'read',
        'attribute1',
        'attribute2',
        'attribute3',
        'attribute4',
        'attribute5',
        'attribute6',
        'attribute7',
        'attribute8',
        'attribute9',
        'attribute10',
        'attribute11',
        'attribute12',
        'attribute13',
        'attribute14',
        'attribute15',
        'attribute16',
        'attribute17',
        'attribute18',
        'attribute19',
        'attribute20'
    ];

    /**
     * Find Unread Count
     *
     * Gets the count of unread messages
     * @param  void
     * @return int
     */
    public function findUnreadCount()
    {
        $this->sql = 'select count(*) unread from message where `read` = \'N\'';

        return (int) $this->findRow()->unread;
    }

    /**
     * Find Unread Messages
     *
     * Gets all unread messages
     * @param  void
     * @return mixed
     */
    public function findUnread()
    {
        $this->makeSelect();
        $this->sql .= 'and `read` = \'N\'';

        return $this->find();
    }

    /**
     * Mark As Read
     *
     * @param  int  $messageId
     * @return void
     */
    public function markAsRead(int $messageId)
    {
        $this->sql = 'update message set read = \'Y\' where id = ?';
        $this->bindValues[] = $messageId;

        return $this->execute();
    }

    /**
     * Mark As Unread
     *
     * @param  int  $messageId
     * @return void
     */
    public function markAsUnread(int $messageId)
    {
        $this->sql = 'update message set read = \'N\' where id = ?';
        $this->bindValues[] = $messageId;

        return $this->execute();
    }
}
