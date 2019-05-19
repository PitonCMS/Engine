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
     * @return array
     */
    public function mergeSettings(array $savedSettings, array $definedSettings)
    {
        // Test if the saved settings are for site or page. Only site settings have the category key
        $pageSetting = isset($savedSettings[0]->category) ? false : true;

        // Make index of saved setting keys to setting array for easy lookup
        $settingIndex = array_combine(array_column($savedSettings, 'setting_key'), array_keys($savedSettings));

        // Loop through defined settings and update with saved values and meta info
        foreach ($definedSettings as $defKey => $setting) {
            if (isset($settingIndex[$setting->key])) {
                $definedSettings[$defKey]->id = $savedSettings[$settingIndex[$setting->key]]->id;
                $definedSettings[$defKey]->setting_value = $savedSettings[$settingIndex[$setting->key]]->setting_value;
                $definedSettings[$defKey]->created_by = $savedSettings[$settingIndex[$setting->key]]->created_by;
                $definedSettings[$defKey]->created_date = $savedSettings[$settingIndex[$setting->key]]->created_date;
                $definedSettings[$defKey]->updated_by = $savedSettings[$settingIndex[$setting->key]]->updated_by;
                $definedSettings[$defKey]->updated_date = $savedSettings[$settingIndex[$setting->key]]->updated_date;

                // Remove saved setting from array parameter now that we have updated the setting definition
                unset($savedSettings[$settingIndex[$setting->key]]);
            } else {
                // If a matching saved setting was NOT found, then set default value
                $definedSettings[$defKey]->setting_value = $definedSettings[$defKey]->value;
            }

            // Amend setting keys to what is expected in template
            $definedSettings[$defKey]->setting_key = $setting->key;
            $definedSettings[$defKey]->input_type = $definedSettings[$defKey]->inputType;

            // Include select options array
            if ($definedSettings[$defKey]->inputType === 'select') {
                $definedSettings[$defKey]->options = array_column($definedSettings[$defKey]->options, 'name', 'value');
            }

            // Add setting catagory. Not needed for page settings, but not in the way either
            $definedSettings[$defKey]->category = 'custom';

            // Remove JSON keys to avoid confusion in template
            unset($definedSettings[$defKey]->key);
            unset($definedSettings[$defKey]->value);
            unset($definedSettings[$defKey]->inputType);
        }

        // Check remaining saved settings for orphaned settings.
        array_walk($savedSettings, function(&$row) use ($pageSetting) {
            if ($pageSetting || (isset($row->category) && $row->category === 'custom')) {
                $row->orphaned = true;
            }
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
