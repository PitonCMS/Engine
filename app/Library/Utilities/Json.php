<?php
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */
namespace Piton\Library\Utilities;

use Interop\Container\ContainerInterface;
use Webmozart\Json\JsonDecoder;
use Webmozart\Json\ValidationFailedException;

/**
 * Piton Layout JSON Decoder/Encoder and schema validator
 */
class Json extends JsonDecoder
{
    /**
     * Container
     * @var Interop\Container\ContainerInterface
     */
    protected $container;

    /**
     * Current Theme
     * @var string
     */
    protected $theme;

    /**
     * Validation Errors
     * @var array
     */
    protected $errors = [];

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->theme = $container->settings['site']['theme'];

        parent::__construct();
    }

    /**
     * Get Page Definition from JSON
     *
     * Schema validation file: vendor/pitoncms/engine/pageLayoutSchema.json
     * Validation errors are written to $this->errors
     * @param string $pageJsonFile Path to page JSON file
     * @return mixed               JSON Object | null
     */
    public function getPageDefinition($pageJsonFile)
    {
        $validationFile = ROOT_DIR . 'vendor/pitoncms/engine/jsonSchemas/pageSchema.json';

        try {
            return $this->decodeFile($pageJsonFile, $validationFile);
        } catch (\RuntimeException $e) {
            // Runtime errors such as file not found
            $this->errors[] = $e->getMessage();
        } catch (ValidationFailedException $e) {
            // Schema validation errors
            $this->errors[] = $e->getMessage();
        } catch (\Exception $e) {
            // Anything else we did not anticipate
            $this->errors[] = 'Unknown Exception in getPageDefinition()';
            $this->errors[] = $e->getMessage();
        }

        return null;
    }

    /**
     * Get Theme Settings
     *
     * Validation errors are written to $this->errors
     * @param void
     * @return mixed
     */
    public function getThemeSettings()
    {
        // themeSettings.json full path
        $jsonFilePath = ROOT_DIR . "themes/{$this->theme}/definitions/themeSettings.json";
        $validationFile = ROOT_DIR . 'vendor/pitoncms/engine/jsonSchemas/themeSettingsSchema.json';

        try {
            return $this->decodeFile($jsonFilePath, $validationFile);
        } catch (\RuntimeException $e) {
            // Runtime errors such as file not found
            $this->errors[] = $e->getMessage();
        } catch (ValidationFailedException $e) {
            // Schema validation errors
            $this->errors[] = $e->getMessage();
        } catch (\Exception $e) {
            // Anything else we did not anticipate
            $this->errors[] = 'Unknown Exception in getThemeSettings()';
            $this->errors[] = $e->getMessage();
        }

        return null;
    }

    /**
     * Get Collection Defintion
     *
     * Validation errors are written to $this->errors
     * @param  string $collectionJsonFile Path to collection JSON file
     * @return mixed                      JSON Object | null
     */
    public function getCollectionDefinition($collectionJsonFile)
    {
        $validationFile = ROOT_DIR . 'vendor/pitoncms/engine/jsonSchemas/collectionSchema.json';

        try {
            return $this->decodeFile($collectionJsonFile);
        } catch (\RuntimeException $e) {
            // Runtime errors such as file not found
            $this->errors[] = $e->getMessage();
        } catch (ValidationFailedException $e) {
            // Schema validation errors
            $this->errors[] = $e->getMessage();
        } catch (\Exception $e) {
            // Anything else we did not anticipate
            $this->errors[] = 'Unknown Exception in getThemeSettings()';
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
