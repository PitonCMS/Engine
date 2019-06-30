<?php
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */
namespace Piton\Library\Handlers;

use Webmozart\Json\JsonDecoder;
use Webmozart\Json\ValidationFailedException;
use Exception;
use RuntimeException;
use FilesystemIterator;

/**
 * Piton JSON Definition File Loader and Validator
 */
class Definition extends JsonDecoder
{
    /**
     * JSON Decoder
     * @var Webmozart\Json\JsonDecoder
     */
    protected $decoder;

    /**
     * Definition Files
     * @var array
     */
    protected $definition = [
        'elements' => ROOT_DIR . 'structure/definitions/elements/',
        'pages' => ROOT_DIR . 'structure/definitions/pages/',
        'navigation' => ROOT_DIR . 'structure/definitions/navigation.json',
        'siteSettings' => ROOT_DIR . 'structure/definitions/siteSettings.json',
        'seededSettings' => ROOT_DIR . 'vendor/pitoncms/engine/config/settings.json',
    ];

    /**
     * Validation Files
     * @var array
     */
    protected $validation = [
        'element' => ROOT_DIR . 'vendor/pitoncms/engine/jsonSchemas/elementSchema.json',
        'page' => ROOT_DIR . 'vendor/pitoncms/engine/jsonSchemas/pageSchema.json',
        'navigation' => ROOT_DIR . 'vendor/pitoncms/engine/jsonSchemas/navigationSchema.json',
        'settings' => ROOT_DIR . 'vendor/pitoncms/engine/jsonSchemas/settingsSchema.json',
    ];

    /**
     * Validation Errors
     * @var array
     */
    protected $errors = [];

    /**
     * Constructor
     *
     * @param  Webmozart\Json\JsonDecoder $jsonDecoder
     * @return void
     */
    public function __construct(JsonDecoder $jsonDecoder)
    {
        $this->decoder = $jsonDecoder;
    }

    /**
     * Decode JSON
     *
     * Validation errors available from getErrorMessages()
     * @param string $json   Path to page JSON file to decode
     * @param string $schema Name of validation: settings|page|element
     * @return mixed         Object|null
     */
    public function decodeJson(string $json, string $schema = null)
    {
        try {
            return $this->decoder->decodeFile($json, $schema);
        } catch (RuntimeException $e) {
            // Runtime errors such as file not found
            $this->errors[] = $e->getMessage();
        } catch (ValidationFailedException $e) {
            // Schema validation errors
            $this->errors[] = $e->getMessage();
        } catch (Exception $e) {
            // Anything else we did not anticipate
            $this->errors[] = 'Unknown Exception';
            $this->errors[] = $e->getMessage();
        }

        return null;
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
        return $this->decodeJson($this->definition['siteSettings'], $this->validation['settings']);
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
        return $this->decodeJson($this->definition['seededSettings'], $this->validation['settings']);
    }

    /**
     * Get Navigation
     *
     * @param  void
     * @return mixed
     */
    public function getNavigation()
    {
        return $this->decodeJson($this->definition['navigation'], $this->validation['navigation']);
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
        return $this->decodeJson($this->definition['pages'] . $pageDefinition, $this->validation['page']);
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
        return $this->decodeJson($this->definition['elements'] . $elementDefinition, $this->validation['element']);
    }

    /**
     * Get All Elements
     *
     * Get all element definitions
     * @param  void
     * @return mixed
     */
    public function getElements()
    {
        // Get all JSON files in directory
        $elements = [];
        foreach ($this->getDirectoryFiles($this->definition['elements']) as $file) {
            if (null === $definition = $this->decodeJson($this->definition['elements'] . $file['filename'], $this->validation['element'])) {
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
     * @param  string $templateType 'page' | 'collection' | null
     * @return array                Array of page templates
     */
    protected function getPageDefinitions(string $templateType)
    {
        $templates = [];
        foreach ($this->getDirectoryFiles($this->definition['pages']) as $file) {
            // Get all definition files
            if (null === $definition = $this->decodeJson($this->definition['pages'] . $file['filename'], $this->validation['page'])) {
                throw new Exception('PitonCMS: Page definition JSON exception ' . print_r($this->getErrorMessages(), true));
                break;
            }

            // Filter our unneeded templates
            if ($definition->templateType !== $templateType) {
                continue;
            }

            $templates[] = [
                'filename' => $file['filename'],
                'name' => $definition->templateName,
                'description' => $definition->templateDescription
            ];
        }

        return $templates;
    }

    /**
     * Get Directory Files
     *
     * Scans a given directory, and returns a multi-dimension array of file names
     * Ignores '.' '..' and sub directories by default
     * $ignore accepts file names or regex patterns to ignore
     * @param  string $dirPath Path to directory to scan
     * @param  mixed  $ignore  String | Array
     * @return array
     */
    protected function getDirectoryFiles($dirPath, $ignore = null)
    {
        $files = [];
        $pattern = '/^\..+'; // Ignore all dot files by default
        $splitCamelCase = '/(?<=[a-z])(?=[A-Z])|(?<=[A-Z])(?=[A-Z][a-z])/';
        $ignoreDirectories = false;

        if (is_string($ignore) && !empty($ignore)) {
            // If 'dir' or 'directory' strings are set, then set flag to ignore directories
            if ($ignore === 'dir' || $ignore === 'directory') {
                $ignoreDirectories = true;
            } else {
                // Add it to the regex
                $pattern .= '|' . $ignore;
            }
        } elseif (is_array($ignore)) {
            // If 'dir' or 'directory' strings are set, then set flag to ignore directories
            if (in_array('dir', $ignore) || in_array('directory', $ignore)) {
                $ignoreDirectories = true;
                $ignore = array_diff($ignore, ['dir', 'directory']);
            }

            // Add it to the regex
            $multiIgnores = implode('|', $ignore);
            $pattern .= empty($multiIgnores) ? '' : '|' . $multiIgnores;
        }

        $pattern .= '/'; // Close regex

        if (is_dir($dirPath)) {
            foreach (new FilesystemIterator($dirPath) as $dirObject) {
                if (($ignoreDirectories && $dirObject->isDir()) || preg_match($pattern, $dirObject->getFilename())) {
                    continue;
                }

                $baseName = $dirObject->getBasename('.' . $dirObject->getExtension());
                $readableFileName = preg_replace($splitCamelCase, '$1 ', $baseName);
                $readableFileName = ucwords($readableFileName);

                $files[] = [
                    'filename' => $dirObject->getFilename(),
                    'basename' => $baseName,
                    'readname' => $readableFileName
                ];
            }
        }

        return $files;
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
