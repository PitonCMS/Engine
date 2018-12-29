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
    protected $modifiableColumns = ['email', 'admin'];

    /**
     * Insert a Record
     *
     * Sets IGNORE in insert to avoid duplication of email addresses
     * @param  DomainObject $domainObject
     * @param  bool                       If true, update on duplicate record
     * @return mixd                       DomainObject | null
     */
    public function insert(DomainObject $domainObject, $ignore = false)
    {
        return $this->coreInsert($domainObject, true);
    }
}
