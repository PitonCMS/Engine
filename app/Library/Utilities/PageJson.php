<?php
/**
 * Piton Page Layout JSON Decoder/Encoder and schema validator
 *
 */
namespace Piton\Library\Utilities;

use Interop\Container\ContainerInterface;
use Webmozart\Json\JsonDecoder;
use Webmozart\Json\ValidationFailedException;

class PageJson extends JsonDecoder
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
     * Get Page Layout Definition from JSON
     *
     * Schema validation file: vendor/pitoncms/engine/pageLayoutSchema.json
     * Validation errors are written to $this->errors
     * @param string Page name
     * @return mixed
     */
    public function getPageLayoutDefinition($pageLayout)
    {
        // Page layout name and get path
        $layoutFile = pathinfo($pageLayout, PATHINFO_FILENAME);
        $jsonFilePath = ROOT_DIR . 'themes/' . $this->theme . '/templates/layouts/' . $layoutFile . '.json';
        $validationFile = ROOT_DIR . 'vendor/pitoncms/engine/pageLayoutSchema.json';

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
            $this->errors[] = 'Unknown Exception in $PageJson->getPageLayoutDefinition()';
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
