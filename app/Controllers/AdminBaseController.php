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
        $session = $this->container->sessionHandler;

        // Make sure severity level is in our CSS
        $severityList = ['primary','secondary','success','danger','warning','info'];
        if (!in_array($severity, $severityList)) {
            throw new \Exception("Alert severity not found in list.");
        }

        $alert = [
            'severity' => $severity,
            'heading' => $heading,
            'message' => (is_array($message)) ? implode(', ', $message) : $message
        ];

        // Alert data is made available in the template, or if $this->redirect() is called
        // then data persists in flash data for one page view
        $this->alert = $alert;

        return;
    }

    /**
     * Merge Saved Settings with JSON Settings
     *
     * Merge saved settings with those from page JSON definition file
     * @param  array  $settings     Saved settings array
     * @param  array  $jsonSettings Defined settings in JSON definition file
     * @param  string $scope        page | theme
     * @return array
     */
    public function mergeSettingsWithJsonSettings(array $settings, array $jsonSettings, string $scope)
    {
        $mapper = $this->container->dataMapper;
        $settingMapper = $mapper('SettingMapper');
        $pageSettingMapper = $mapper('PageSettingMapper');

        // Validate that we have a valid scope
        if (!in_array($scope, ['page','theme'])) {
            throw new Exception('Invalid $scope paramter');
        }

        // Update settings from DB with details from JSON file by matching keys
        foreach ($settings as $settingIndex => $setting) {
            // Skip ahead if this is not a theme setting
            if (isset($setting->category) && $setting->category !== 'theme') {
                continue;
            }

            // Now see if we have a matching JSON setting definition
            foreach ($jsonSettings as $jsonIndex => $jsonSetting) {
                if ($setting->setting_key === $jsonSetting->key) {
                    // Update display properties using JSON definition as the master reference
                    $setting->sort_order = isset($jsonSetting->sort) ? $jsonSetting->sort : null;
                    $setting->label = $jsonSetting->label;
                    $setting->input_type = isset($jsonSetting->inputType) ? $jsonSetting->inputType : 'input';
                    $setting->help = isset($jsonSetting->help) ? $jsonSetting->help : null;

                    // Include select options array
                    if ($jsonSetting->inputType === 'select') {
                        $setting->options = array_column($jsonSetting->options, 'name', 'value');
                    }

                    // Unset this JSON setting and skip to the next outer loop iteration
                    unset($jsonSettings[$jsonIndex]);
                    continue 2;
                }
            }

            // Found an orphaned field setting in the DB, so mark it as such for optional delete
            $settings[$settingIndex]->orphaned = true;

            if ($settings[$settingIndex]->input_type = 'select') {
                // For readability purposes, change orphaned selects to inputs
                $settings[$settingIndex]->input_type = 'input';
            }
        }

        // Any remaining JSON settings are new and have not yet been saved to the DB
        // Append these to the settings array
        foreach ($jsonSettings as $setting) {
            // Create setting object
            if ($scope === 'theme') {
                $newSetting = $settingMapper->make();
                $newSetting->category = 'theme';
            } else {
                $newSetting = $pageSettingMapper->make();
            }

            $newSetting->sort_order = isset($setting->sort) ? $setting->sort : 1;
            $newSetting->setting_key = $setting->key;
            $newSetting->setting_value = isset($setting->value) ? $setting->value : null;
            $newSetting->input_type = isset($setting->inputType) ? $setting->inputType : 'input';
            $newSetting->label = $setting->label;
            $newSetting->help = isset($setting->help) ? $setting->help : null;

            // Include select options
            if ($setting->inputType === 'select') {
                $newSetting->options = array_column($setting->options, 'name', 'value');
            }

            // Append to array
            $settings[] = $newSetting;
        }

        return $settings;
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
            throw new Exception("PitonCMS Unexpected $templateType paramter. Expecting 'page' or 'collection'");
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
