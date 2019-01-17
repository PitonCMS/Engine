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
        'page_id',
        'scope',
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
        $this->sql .= ' and scope = \'global\' order by category, sort_order';

        return $this->find();
    }
}
