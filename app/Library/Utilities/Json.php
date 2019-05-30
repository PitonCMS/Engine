<?php
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */
namespace Piton\Library\Utilities;

use Webmozart\Json\JsonDecoder;
use Webmozart\Json\ValidationFailedException;
use Exception;
use RuntimeException;

/**
 * Piton Layout JSON Decoder/Encoder and schema validator
 */
class Json extends JsonDecoder
{
    /**
     * Validation Files
     * @var array
     */
    protected $validation = [];

    /**
     * Validation Errors
     * @var array
     */
    protected $errors = [];

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->validation = [
            'setting' => ROOT_DIR . 'vendor/pitoncms/engine/jsonSchemas/settingsSchema.json',
            'page' => ROOT_DIR . 'vendor/pitoncms/engine/jsonSchemas/pageSchema.json',
            'element' => ROOT_DIR . 'vendor/pitoncms/engine/jsonSchemas/elementSchema.json',
            'themes' => ROOT_DIR . 'vendor/pitoncms/engine/jsonSchemas/themesSchema.json',
        ];

        parent::__construct();
    }

    /**
     * Get JSON Definition
     *
     * Validation errors available from getErrorMessages()
     * @param string $jsonFile   Path to page JSON file to decode
     * @param string $jsonSchema Name of validation: settings|page|element|themes
     * @return mixed             Object | null
     */
    public function getJson($jsonFile, $jsonSchema = null)
    {
        if (null !== $jsonSchema && array_key_exists($jsonSchema, $this->validation)) {
            $jsonSchema = $this->validation[$jsonSchema];
        } elseif (null !== $jsonSchema && !array_key_exists($jsonSchema, $this->validation)) {
            throw new Exception('PitonCMS: Invalid jsonSchema validation key provided');
        }

        try {
            return $this->decodeFile($jsonFile, $jsonSchema);
        } catch (RuntimeException $e) {
            // Runtime errors such as file not found
            $this->errors[] = $e->getMessage();
        } catch (ValidationFailedException $e) {
            // Schema validation errors
            $this->errors[] = $e->getMessage();
        } catch (Exception $e) {
            // Anything else we did not anticipate
            $this->errors[] = 'Unknown Exception in getPageDefinition()';
            $this->errors[] = $e->getMessage();
        }

        return null;
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
