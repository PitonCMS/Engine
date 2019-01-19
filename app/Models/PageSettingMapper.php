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
 * Piton Page Setting Mapper
 */
class PageSettingMapper extends DataMapperAbstract
{
    protected $table = 'page_setting';
    protected $modifiableColumns = [
        'page_id',
        'setting_key',
        'setting_value'
    ];

    /**
     * Find Page settings
     *
     * Get page level settings
     * @param  int $pageId  Page ID
     * @return mixed        Array | null
     */
    public function findPageSettings($pageId)
    {
        $this->makeSelect();
        $this->sql .= ' and page_id = ?';
        $this->bindValues[] = $pageId;

        return $this->find();
    }
}
