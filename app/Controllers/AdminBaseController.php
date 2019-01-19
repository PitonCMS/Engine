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
     * @param mixed  $messge   Message or array messages (Optional)
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
            'message' => $message
        ];

        // Alert data is made available in the template, or if $this->redirect() is called
        // then data persists in flash data for one page view
        $this->pageData['alert'] = $alert;

        return;
    }

    /**
     * Merge Settings with JSON Fields
     *
     * Merge saved settings with fields from JSON page definition file
     * @param
     * @return
     */
    public function mergeSettingsWithJsonFields($settings, $jsonFields, $scope)
    {
        $mapper = $this->container->dataMapper;
        $settingMapper = $mapper('settingMapper');

        if (empty($jsonFields)) {
            return $settings;
        }

        // TODO ?
        $themeSettingFlag = false;
        if ($scope == 'global') {
            $themeSettingFlag = true;
        }

        if (!in_array($scope, ['page','global'])) {
            throw new Exception('Invalid $scope paramter');
        }

        // Create union of settings from DB and from JSON file by matching keys
        foreach ($settings as $settingIndex => $setting) {
            // Skip ahead if not in scope
            if ($themeSettingFlag && $setting->category !== 'theme') {
                continue;
            }

            // Now see if we have a field setting from JSON that matches one in the DB
            foreach ($jsonFields as $fieldIndex => $field) {
                if ($setting->setting_key === $field->key) {
                    // There is a match on setting key so update display properties
                    // use JSON definition as the master reference
                    $setting->sort_order = $field->sort;
                    $setting->label = $field->label;
                    $setting->input_type = $field->inputType;
                    $setting->help = $field->help;

                    // Include select options array
                    if ($field->inputType === 'select') {
                        $setting->options = $this->createOptionsArray($field->options);
                    }

                    // Unset field setting and skip to the next outer loop iteration
                    unset($jsonFields[$fieldIndex]);
                    continue 2;
                }
            }

            // Found an orphaned field setting in the DB, so mark it as such for optional delete
            $settings[$settingIndex]->orphaned = true;

            if ($settings[$settingIndex]->input_type = 'select') {
                // For key readability purposes, change orphaned selects to inputs
                $settings[$settingIndex]->input_type = 'input';
            }
        }

        // Any remaining fields are new and have not yet been saved to the DB
        // Append these to the settings array
        foreach ($jsonFields as $setting) {
            // Create setting object
            $newSetting = $settingMapper->make();
            $newSetting->category = ($scope === 'global') ? 'theme' : null;
            $newSetting->sort_order = $setting->sort;
            $newSetting->setting_key = $setting->key;
            $newSetting->setting_value = $setting->value;
            $newSetting->input_type = $setting->inputType;
            $newSetting->label = $setting->label;
            $newSetting->help = $setting->help;

            // Include select options
            if ($setting->inputType === 'select') {
                $newSetting->options = $this->createOptionsArray($setting->options);
            }

            // Append to array
            $settings[] = $newSetting;
        }

        return $settings;
    }

    /**
     * Options Associative Array
     *
     * Create select options associative array
     * @param  array $options
     * @return array          Associative array [$value] = $name
     */
    protected function createOptionsArray($options)
    {
        $newArray = [];
        foreach ($options as $row) {
            $newArray[$row->value] = ($row->name) ?: $row->value;
        }

        return $newArray;
    }
}
