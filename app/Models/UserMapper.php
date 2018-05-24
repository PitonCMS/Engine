<?php
/**
 * User Mapper
 */
namespace Piton\Models;

class UserMapper extends DataMapperAbstract
{
    protected $table = 'user';
    protected $modifiableColumns = ['email'];

    /**
     * Insert a Record
     *
     * Sets IGNORE in insert to avoid duplication of email addresses
     * @param Domain Object
     * @return Domain Object
     */
    public function insert(DomainObject $domainObject)
    {
        return $this->_insert($domainObject, true);
    }
}
