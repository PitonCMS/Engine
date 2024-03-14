<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

namespace Piton\Controllers;

use Piton\Models\Entities\PitonEntity;
use Slim\Http\Response;

/**
 * Piton Front End Base Controller
 *
 * All other front end controllers should extend this class.
 */
class FrontBaseController extends BaseController
{
    /**
     * Supplemental page data from project extensions
     *
     * Expects a 'key' and 'value' as values
     * @var array
     */
    protected $pageDataExtension = [];

    /**
     * Render Front End Template
     *
     * Modifies path to template then calls parent render() method
     * @param string $template Path to template
     * @param mixed  $data   Data to echo, Domain object or array
     * @return Response
     */
    public function render(string $template, $data = null): Response
    {
        return parent::render('pages/' . $template, $data);
    }

    /**
     * Set Extension Page Data
     *
     * Accepts key:value pairs that are saved to $this->pageDataExtension
     * Can be called multiple times
     * @param string $key   Key name
     * @param mixed  $value Value to inject into page data
     * @return void
     */
    public function setExtensionPageData(string $key, $value = null)
    {
        $this->pageDataExtension[$key] = $value;
    }

    /**
     * Merge Extension Data into Page Data
     *
     * If a key & value were set to $this->pageDataExtension property, then that key and value are merged
     * into the provided $page object.
     * @param PitonEntity $page Page object
     * @return PitonEntity
     */
    protected function mergeExtensionPageData(PitonEntity $page): PitonEntity
    {
        // If extension data was set, then add to object and return
        if (!empty($this->pageDataExtension)) {
            foreach ($this->pageDataExtension as $key => $data) {
                $page->{$key} = $data;
            }

            return $page;
        }

        // Otherwise return provided object
        return $page;
    }
}
