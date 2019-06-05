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
use Piton\ORM\DomainObject;

/**
 * Piton User Mapper
 */
class UserMapper extends DataMapperAbstract
{
    protected $table = 'user';
    protected $modifiableColumns = ['email', 'role','active'];

    /**
     * Find Users
     *
     * Sorts by active
     */
    public function findUsers()
    {
        $this->makeSelect();
        $this->sql .= ' order by `active`';

        return $this->find();
    }

    /**
     * Find Active Users
     *
     * Filters out inactive user
     */
    public function findActiveUsers()
    {
        $this->makeSelect();
        $this->sql .= ' and `active` = \'Y\'';

        return $this->find();
    }
}
