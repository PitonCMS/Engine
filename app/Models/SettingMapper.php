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
    protected $inCategories = '(\'site\',\'contact\',\'social\',\'piton\')';
    protected $table = 'setting';
    protected $modifiableColumns = [
        'category',
        'reference_id',
        'setting_key',
        'setting_value',
    ];

    /**
     * Find Settings
     *
     * Find all settings, or a category of settings
     * @param  $category site|contact|social|piton
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

        return $this->find();
    }

    /**
     * Find Page Settings
     *
     * Get page level settings
     * @param  int   $pageId  Page ID
     * @return mixed          Array | null
     */
    public function findPageSettings(int $pageId)
    {
        $this->makeSelect();
        $this->sql .= ' and category = \'page\' and reference_id = ?';
        $this->bindValues[] = $pageId;

        return $this->find();
    }

    /**
     * Delete Page Settings
     *
     * @param  int  $pageId Page ID
     * @return bool
     */
    public function deleteByPageId(int $pageId)
    {
        $this->sql = "delete from {$this->table} where category = \'page\' and reference_id = ?;";
        $this->bindValues[] = $pageId;

        return $this->execute();
    }
}
