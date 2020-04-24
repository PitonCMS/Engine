<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

namespace Piton\Library\Handlers;

use Exception;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Piton JSON Definition File Loader and Validator
 */
class Definition
{
    /**
     * JSON Validator
     * @var object
     */
    protected $validator;

    /**
     * Definition File Paths
     * @var array
     */
    protected $definition = [
        'elements' => ROOT_DIR . 'structure/templates/elements/',
        'pages' => ROOT_DIR . 'structure/templates/pages/',
        'navigation' => ROOT_DIR . 'structure/definitions/navigation.json',
        'siteSettings' => ROOT_DIR . 'structure/definitions/siteSettings.json',
        'seededSettings' => ROOT_DIR . 'vendor/pitoncms/engine/config/settings.json',
    ];

    /**
     * Validation File Paths
     * @var array
     */
    protected $validation = [
        'element' => ROOT_DIR . 'vendor/pitoncms/engine/jsonSchemas/definitions/elementSchema.json',
        'page' => ROOT_DIR . 'vendor/pitoncms/engine/jsonSchemas/definitions/pageSchema.json',
        'navigation' => ROOT_DIR . 'vendor/pitoncms/engine/jsonSchemas/definitions/navigationSchema.json',
        'settings' => ROOT_DIR . 'vendor/pitoncms/engine/jsonSchemas/definitions/settingsSchema.json',
    ];

    /**
     * Validation Errors
     * @var array
     */
    protected $errors = [];

    /**
     * Constructor
     *
     * @param  object JSON Validator
     * @return void
     */
    public function __construct(object $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Get Custom Site Settings
     *
     * Get site settings
     * @param  void
     * @return mixed
     */
    public function getSiteSettings()
    {
        return $this->decodeValidJson($this->definition['siteSettings'], $this->validation['settings']);
    }

    /**
     * Get Seeded Site Settings
     *
     * Get site settings
     * @param  void
     * @return mixed
     */
    public function getSeededSiteSettings()
    {
        return $this->decodeValidJson($this->definition['seededSettings'], $this->validation['settings']);
    }

    /**
     * Get Navigation
     *
     * @param  void
     * @return mixed
     */
    public function getNavigation()
    {
        return $this->decodeValidJson($this->definition['navigation'], $this->validation['navigation']);
    }

    /**
     * Get Page
     *
     * Get page definition
     * @param  string $pageDefinition
     * @return mixed
     */
    public function getPage(string $pageDefinition)
    {
        return $this->decodeValidJson($this->definition['pages'] . $pageDefinition, $this->validation['page']);
    }

    /**
     * Get All Pages
     *
     * Get page definitions
     * @param  void
     * @return mixed
     */
    public function getPages()
    {
        return $this->getPageDefinitions('page');
    }

    /**
     * Get All Collections
     *
     * Get collection definitions
     * @param  void
     * @return mixed
     */
    public function getCollections()
    {
        return $this->getPageDefinitions('collection');
    }

    /**
     * Get Single Element
     *
     * Get element definition
     * @param  string $elementDefinition
     * @return mixed
     */
    public function getElement(string $elementDefinition)
    {
        return $this->decodeValidJson($this->definition['elements'] . $elementDefinition, $this->validation['element']);
    }

    /**
     * Get All Elements
     *
     * Get all element definitions
     * @param  void
     * @return array
     */
    public function getElements(): array
    {
        // Get all Element JSON files in directory
        $elements = [];
        foreach ($this->getDirectoryDefinitionFiles($this->definition['elements']) as $file) {
            if (null === $definition = $this->decodeValidJson($this->definition['elements'] . $file['filename'], $this->validation['element'])) {
                throw new Exception('PitonCMS: Element JSON definition error: ' . print_r($this->getErrorMessages(), true));
            } else {
                $definition->filename = $file['filename'];
                $elements[] = $definition;
            }
        }

        return $elements;
    }

    /**
     * Get Page or Collection Definitions
     *
     * Get available templates from JSON files. If no param is provided, then all templates are returned
     * @param  string $templateType page|collection
     * @return array                Array of page templates
     */
    protected function getPageDefinitions(string $templateType): array
    {
        $templates = [];
        foreach ($this->getDirectoryDefinitionFiles($this->definition['pages']) as $file) {
            // Get definition file
            if (null === $definition = $this->decodeValidJson($this->definition['pages'] . $file, $this->validation['page'])) {
                $this->errors[] = "PitonCMS: Unable to read page definition file: $file";
                break;
            }

            // Filter out unneeded templates
            if (!empty($definition->templateType) && $definition->templateType !== $templateType) {
                continue;
            }

            $templates[] = [
                'filename' => $file,
                'name' => $definition->templateName,
                'description' => $definition->templateDescription
            ];
        }

        return $templates;
    }

    /**
     * Get Directory Definition Files
     *
     * Recursively scans a given template directory.
     * Returns an array of JSON definition files with path relative to $dirPath
     * @param  string $dirPath Path to directory to scan
     * @return array
     */
    protected function getDirectoryDefinitionFiles($dirPath): array
    {
        $files = [];
        $dirPathLength = mb_strlen($dirPath);
        $directories = new RecursiveDirectoryIterator($dirPath, RecursiveDirectoryIterator::SKIP_DOTS);
        $filter = new RecursiveCallbackFilterIterator($directories, function ($current, $key, $iterator) {
            // Ensures we scan recursively
            if ($iterator->hasChildren()) {
                return true;
            }

            if ($current->getExtension() === 'json') {
                return true;
            }

            return false;
        });

        foreach (new RecursiveIteratorIterator($filter) as $file) {
            // Return path relative to $dirPath
            $files[] = mb_substr($file->getPathname(), $dirPathLength);
        }

        return $files;
    }

    /**
     * Decode and Validate JSON Definition
     *
     * Validation errors available from getErrorMessages()
     * @param string $json   Path to page JSON file to decode
     * @param string $schema Path to validation JSON Schema file
     * @return object
     */
    protected function decodeValidJson(string $json, string $schema): ?object
    {
        // Get and decode JSON to be validated
        if (false === $contents = file_get_contents($json)) {
            throw new Exception("PitonCMS Definition Exception: Unable to get file contents. $json");
        }
        $jsonDecodedInput = json_decode($contents, false, 512, JSON_THROW_ON_ERROR);

        $this->validator->validate($jsonDecodedInput, (object)['$ref' => 'file://' . $schema]);
        if ($this->validator->isValid()) {
            return $jsonDecodedInput;
        }

        // If not valid, record error messages and return null
        foreach ($this->validator->getErrors() as $error) {
            $this->errors[] =  sprintf("[%s] %s", $error['property'], $error['message']);
        }

        return null;
    }

    /**
     * Get Errors
     *
     * Returns array of error messages
     * @param void
     * @return array
     */
    public function getErrorMessages(): array
    {
        return $this->errors;
    }
}
