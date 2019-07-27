<?php
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */
namespace Piton\Library\Handlers;

use JsonSchema\Validator;
use JsonSchema\Constraints\Constraint;
use Piton\ORM\DomainObject;

/**
 * Validation Handler
 *
 * Manages Entity Object Data Validation
 */
class Validation
{
    /**
     * Table Validation Schemas
     * @var array
     */
    protected $tableSchemas = [
        'setting' => ROOT_DIR . 'vendor/pitoncms/engine/jsonSchemas/validations/setting.json',
    ];

    /**
     * Error Messages Array
     * @var array
     */
    protected $errors = [];

    /**
     * Validation Engine
     * @var JsonSchema\Validator
     */
    protected $validator;

    /**
     * Constructor
     *
     * @param object $validator JsonSchema\Validator
     * @return void
     */
    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Validate Object
     *
     * Accepts Piton ORM DomainObject to validate
     * @param object $entity DomainObject
     * @param string $table
     * @return bool
     */
    public function validate(DomainObject $entity, string $table)
    {
        $this->validator->validate($entity, (object)['$ref' => 'file://' . $this->tableSchemas[$table]], Constraint::CHECK_MODE_COERCE_TYPES);

        if ($this->validator->isValid()) {
            return true;
        }

        // If not valid, record error messages before returning false
        foreach ($this->validator->getErrors() as $error) {
            $this->errors[] =  sprintf("[%s = %s] %s",
                $error['property'],
                $entity->{$error['property']},
                $error['message']
            );
        }

        return false;
    }

    /**
     * Get Errors
     *
     * @param void
     * @return array
     */
    public function getErrorMessages()
    {
        return $this->errors;
    }
}
