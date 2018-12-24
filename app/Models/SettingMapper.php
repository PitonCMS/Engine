<?php
/**
 * Setting Mapper
 */
namespace Piton\Models;

class SettingMapper extends DataMapperAbstract
{
    protected $table = 'setting';
    protected $modifiableColumns = ['setting_value'];
}
