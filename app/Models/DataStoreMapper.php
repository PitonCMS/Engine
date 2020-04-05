<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

namespace Piton\Models;

use Piton\ORM\DataMapperAbstract;

/**
 * Piton Data Store Mapper
 *
 * Stores key-value pairs
 */
class DataStoreMapper extends DataMapperAbstract
{
    protected $inCategories = "('site','contact','social','piton')";
    protected $table = 'data_store';
    protected $modifiableColumns = ['category', 'page_id', 'setting_key', 'setting_value'];

    /**
     * Find Settings
     *
     * Find all settings, or a category of settings
     * @param  $category site|contact|social|piton
     * @return array|null
     */
    public function findSiteSettings(string $category = null): ?array
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
     * @return array|null
     */
    public function findPageSettings(int $pageId): ?array
    {
        $this->makeSelect();
        $this->sql .= ' and category = \'page\' and page_id = ?';
        $this->bindValues[] = $pageId;

        return $this->find();
    }

    /**
     * Delete Page Settings
     *
     * @param  int  $pageId Page ID
     * @return bool
     */
    public function deleteByPageId(int $pageId): bool
    {
        $this->sql = "delete from {$this->table} where category = 'page' and page_id = ?;";
        $this->bindValues[] = $pageId;

        return $this->execute();
    }
}
