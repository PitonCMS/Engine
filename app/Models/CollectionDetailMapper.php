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
 * Piton Collection Detail Mapper
 */
class CollectionDetailMapper extends DataMapperAbstract
{
    protected $table = 'collection_detail';
    protected $modifiableColumns = [
        'collection_id',
        'url',
        'title',
        'sub_title',
        'content_raw',
        'content',
        'expansion',
        'summary_image_path',
        'detail_image_path',
        'published_date'
    ];
    protected $domainObjectClass = __NAMESPACE__ . '\CollectionDetail';

    /**
     * Find Collection Details by ID
     *
     * @param  int   $collectionId       Collection ID
     * @param  bool  $includeUnpublished Include unpublished collection details
     * @return mixed                     Array DomainObjects | null
     */
    public function findByCollectionId($collectionId, $includeUnpublished = false)
    {
        $this->makeSelect();
        $this->sql .= ' and collection_id = ?';
        $this->bindValues[] = $collectionId;

        if (!$includeUnpublished) {
            $this->sql .= " and published_date <= '{$this->today()}'";
        }

        $this->sql .= 'order by sort';

        return $this->find();
    }

    /**
     * Delete Collection Details
     *
     * Delete all collection details given a collection ID
     * @param  int   $collectionId Collection ID
     * @return mixed               true | null
     */
    public function deleteByCollectionId($collectionId)
    {
        $this->sql = "delete from {$this->table} where collection_id = ?;";
        $this->bindValues[] = $collectionId;

        return $this->execute();
    }
}
