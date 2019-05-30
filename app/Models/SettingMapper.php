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
 * Piton Setting Mapper
 */
class SettingMapper extends DataMapperAbstract
{
    protected $table = 'setting';
    protected $modifiableColumns = [
        'category',
        'sort',
        'setting_key',
        'setting_value',
    ];

    /**
     * Find All Settings
     *
     * Find all settings
     * @param void
     * @return array
     */
    public function findSiteSettings()
    {
        $this->makeSelect();
        $this->sql .= ' order by sort';

        return $this->find();
    }
}
