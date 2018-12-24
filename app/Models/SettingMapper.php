<?php
/**
 * Setting Mapper
 */
namespace Piton\Models;

class SettingMapper extends DataMapperAbstract
{
    protected $table = 'setting';
    protected $modifiableColumns = ['setting_value'];

    /**
     * Find All Settings
     *
     * Find all settings, in order of setting category and sort
     * @param void
     * @return array
     */
    public function findSiteSettings()
    {
        $this->makeSelect();
        $this->sql .= ' order by category, sort_order';

        return $this->find();
    }
}
