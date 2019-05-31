<?php
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */
namespace Piton\Controllers;

use Exception;

/**
 * Piton Admin Base Controller
 *
 * All other admin controllers should extend this class.
 */
class AdminBaseController extends BaseController
{
    /**
     * Render Admin Template
     *
     * Modifies path to layout then calls parent render() method
     * @param string $layout Path to layout
     * @param mixed  $data   Data to echo, Domain object or array
     */
    public function render($layout, $data = null)
    {
        return parent::render('@admin/' . $layout, $data);
    }

    /**
     * Set Alert
     *
     * Set alert using flash data to session
     * @param string $severity Severity level
     * @param string $heading  Heading text
     * @param mixed  $messge   Message or array of messages (Optional)
     * @return void
     */
    public function setAlert($severity, $heading, $message = null)
    {
        // Make sure severity level is in our Bootstrap CSS
        $severityList = ['primary','secondary','success','danger','warning','info'];
        if (!in_array($severity, $severityList)) {
            throw new Exception("Alert severity not found in list.");
        }

        // Alert data is made available in the template, or if $this->redirect() is called
        // then data persists in flash data for one page view
        $this->alert = [
            'severity' => $severity,
            'heading' => $heading,
            'message' => (is_array($message)) ? $message : [$message]
        ];
    }

    /**
     * Append Alert Message
     *
     * Add additional messages to alert dialog
     * @param  string $message
     * @return void
     */
    public function appendAlertMessage(string $message)
    {
        $this->alert['message'][] = $message;
    }

    /**
     * Merge Saved Settings with Defined Settings
     *
     * Merge saved settings with those from page JSON definition file
     * @param  array  $savedSettings   Saved settings array
     * @param  array  $definedSettings Defined settings in JSON definition file
     * @param  string $category        Optional category to filter setting definitions
     * @return array
     */
    public function mergeSettings(array $savedSettings, array $definedSettings, string $category = null)
    {
        // Make index of saved setting keys to setting array for easy lookup
        $settingIndex = array_combine(array_column($savedSettings, 'setting_key'), array_keys($savedSettings));

        // Loop through defined settings and update with saved values and meta info
        foreach ($definedSettings as $key => &$setting) {
            // Check category filter and remove unrelated settings
            if (isset($category) && $category !== $setting->category) {
                unset($definedSettings[$key]);
                continue;
            }

            // If we have a matching saved setting key, then update who columns and ID
            if (isset($settingIndex[$setting->key])) {
                $setting->id = $savedSettings[$settingIndex[$setting->key]]->id;
                $setting->setting_value = $savedSettings[$settingIndex[$setting->key]]->setting_value;
                $setting->created_by = $savedSettings[$settingIndex[$setting->key]]->created_by;
                $setting->created_date = $savedSettings[$settingIndex[$setting->key]]->created_date;
                $setting->updated_by = $savedSettings[$settingIndex[$setting->key]]->updated_by;
                $setting->updated_date = $savedSettings[$settingIndex[$setting->key]]->updated_date;

                // Remove saved setting from array parameter now that we have updated the setting definition
                unset($savedSettings[$settingIndex[$setting->key]]);
            } else {
                // If a matching saved setting was NOT found, then set default value
                $setting->setting_value = $setting->value;
                $setting->status = 'new';
            }

            // Amend setting keys to what is expected in template
            $setting->setting_key = $setting->key;
            $setting->input_type = $setting->inputType;

            // Include select options array
            if ($setting->inputType === 'select') {
                $setting->options = array_column($setting->options, 'name', 'value');
            }

            // Remove JSON keys to avoid confusion in template
            unset($setting->key);
            unset($setting->value);
            unset($setting->inputType);
        }

        // Check remaining saved settings for orphaned settings
        array_walk($savedSettings, function(&$row) {
            $row->status = 'orphaned';
        });

        // Append defined settings to end of saved settings array and return
        return array_merge($savedSettings, $definedSettings);
    }

    /**
     * Get Page or Collection Templates
     *
     * Get available templates from JSON files. If no param is provided, then all templates are returned
     * @param  string $templateType 'page' | 'collection' | null
     * @return array                Array of page templates
     */
    public function getPageTemplates(string $templateType = null)
    {
        $toolbox = $this->container->toolbox;
        $json = $this->container->json;

        // Validate inputs
        if ($templateType !== null && !in_array($templateType, ['page','collection'])) {
            throw new Exception("PitonCMS: Unexpected $templateType paramter. Expecting 'page' or 'collection'");
        }

        $jsonPath = ROOT_DIR . "structure/definitions/pages/";
        $templates = [];

        foreach ($toolbox->getDirectoryFiles($jsonPath) as $row) {
            // Get definition files
            if (null === $definition = $json->getJson($jsonPath . $row['filename'], 'page')) {
                $this->setAlert('danger', 'Page JSON Definition Error', $json->getErrorMessages());
                break;
            }

            if ($templateType !== null && $definition->templateType !== $templateType) {
                continue;
            }

            $templates[] = [
                'filename' => $row['filename'],
                'name' => $definition->templateName,
                'description' => $definition->templateDescription
            ];
        }

        return $templates;
    }
}
