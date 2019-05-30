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
    protected $inCategories = '(\'site\',\'contact\',\'social\')';
    protected $table = 'setting';
    protected $modifiableColumns = [
        'category',
        'reference_id',
        'sort',
        'setting_key',
        'setting_value',
    ];

    /**
     * Find Settings
     *
     * Find all settings, or a category of settings
     * @param  $category 'site'|'contact'|'social'
     * @return array
     */
    public function findSiteSettings(string $category = null)
    {
        $this->makeSelect();
        if (null === $category) {
            $this->sql .= ' and category in ' . $this->inCategories;
        } else {
            $this->sql .= ' and category = ?';
            $this->bindValues[] = $category;
        }

        $this->sql .= ' order by sort';

        return $this->find();
    }
}
