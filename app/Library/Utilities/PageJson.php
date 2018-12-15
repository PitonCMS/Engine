<?php
/**
 * Piton Page Layout JSON Decoder/Encoder and schema validator
 *
 */
namespace Piton\Library\Utilities;

use Interop\Container\ContainerInterface;
use Webmozart\Json\JsonDecoder;

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
     * @var arrah
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
    }

    /**
     * Get Page Layout Definition from JSON
     *
     * @param string Page name
     * @return mixed
     */
    public function getPageLayoutDefinition($pageLayout)
    {
        // Validate page layout name and get path
        $layoutFile = pathinfo($pageLayout, PATHINFO_FILENAME);
        $jsonPath = ROOT_DIR . 'themes/' . $this->theme . '/templates/layouts/' . $layoutFile . '.json';

        try {
            return $this->decodeFile($jsonPath);
        } catch (\RuntimeException $e) {
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
