<?php
/**
 * Setting Mapper
 */
namespace Piton\Models;

class SettingMapper extends DataMapperAbstract
{
    protected $table = 'setting';
    protected $modifiableColumns = [
        'category',
        'sort_order',
        'setting_key',
        'setting_value',
        'input_type',
        'label',
        'help'
    ];

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

    /**
     * Find All Custom Settings
     *
     * Find all custom settings to CRUD
     * @param void
     * @return mixed Array | null
     */
    public function findCustomSettings()
    {
        $this->makeSelect();
        $this->sql .= ' where setting.category = \'custom\' order by sort_order';

        return $this->find();
    }
}
