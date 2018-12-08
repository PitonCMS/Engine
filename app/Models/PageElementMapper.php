<?php
/**
 * Page Element Mapper
 */
namespace Piton\Models;

class PageElementMapper extends DataMapperAbstract
{
    protected $table = 'page_element';
    protected $modifiableColumns = [
        'element_type',
        'title',
        'content_raw',
        'content',
        'collection_id',
        'media_id',
        'media_path'
    ];
}
