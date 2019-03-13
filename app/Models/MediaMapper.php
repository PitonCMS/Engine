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
        'file',
        'caption',
    ];
}
