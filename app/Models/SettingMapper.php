<?php
/**
 * Setting Mapper
 */
namespace Piton\Models;

class SettingMapper extends DataMapperAbstract
{
    protected $table = 'setting';
    protected $modifiableColumns = ['category', 'setting_key', 'setting_value'];

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
