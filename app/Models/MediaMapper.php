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
 * Piton Media Mapper
 */
class MediaMapper extends DataMapperAbstract
{
    protected $table = 'media';
    protected $modifiableColumns = [
        'filename',
        'width',
        'height',
        'feature',
        'caption',
    ];
    protected $domainObjectClass = __NAMESPACE__ . '\Entities\Media';

    /**
     * Find Media By Category ID
     *
     * Find media by category ID
     * @param  int   $catId
     * @return mixed
     */
    public function findMediaByCategoryId(int $catId = null)
    {
        if (null === $catId) {
            return;
        }

        $this->sql = <<<SQL
select
    mc.category,
    m.id,
    m.filename,
    m.width,
    m.height,
    m.feature,
    m.caption
from media_category mc
join media_category_map mcp on mc.id = mcp.category_id
join media m on mcp.media_id = m.id
where mc.id = ?
SQL;
        $this->bindValues[] = $catId;

        return $this->find();
    }
}
