<?php
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */
namespace Piton\Controllers;

/**
 * Admin Setting Controller
 *
 * Manage site level application settings
 */
class AdminSettingController extends AdminBaseController
{
    /**
     * Manage Site Settings
     *
     * List all site configuration settings to bulk edit
     */
    public function showSettings()
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $SettingMapper = $mapper('SettingMapper');
        $Json = $this->container->json;

        // Fetch settings from database
        $allSettings = $SettingMapper->findSiteSettings();

        // Fetch custom theme settings from themes/<theme-name>/themeSettings.json
        if (null === $customSettings = $Json->getThemeSettings()) {
            $this->setAlert('danger', 'Custom Theme Settings Error', $Json->getErrorMessages());
        }

        // Create union of settings from DB and from JSON file by matching keys
        foreach ($allSettings as $allKey => $setting) {
            // Skip ahead if non-custom setting
            if ($setting->category !== 'custom') {
                continue;
            }

            // Now see if we have a custom setting from JSON that matches one in the DB
            foreach ($customSettings as $customKey => $custom) {
                if ($setting->setting_key === $custom->key) {
                    // There is a match on setting key so update display properties
                    // use themeSettings.json as the master reference
                    $setting->sort_order = $custom->sort;
                    $setting->label = $custom->label;
                    $setting->input_type = $custom->inputType;
                    $setting->help = $custom->help;

                    // Include select options array
                    if ($custom->inputType === 'select') {
                        $setting->options = $this->createOptionsArray($custom->options);
                    }

                    // Unset custom setting and skip to the next outer loop iteration
                    unset($customSettings[$customKey]);
                    continue 2;
                }
            }

            // Found an orphaned custom setting in the DB, so mark it as such for optional delete
            $allSettings[$allKey]->orphaned = true;

            if ($allSettings[$allKey]->input_type = 'select') {
                // For readability purposes, change orphaned selects to inputs
                $allSettings[$allKey]->input_type = 'input';
            }
        }

        // Any remaining $customSettings are new and have not yet been saved to the DB
        // Append these to the settings array
        foreach ($customSettings as $custom) {
            // Create setting object
            $newSetting = $SettingMapper->make();
            $newSetting->category = 'custom';
            $newSetting->sort_order = $custom->sort;
            $newSetting->setting_key = $custom->key;
            $newSetting->setting_value = $custom->value;
            $newSetting->input_type = $custom->inputType;
            $newSetting->label = $custom->label;
            $newSetting->help = $custom->help;

            // Include select options
            if ($custom->inputType === 'select') {
                $newSetting->options = $this->createOptionsArray($custom->options);
            }

            // Append to array
            $allSettings[] = $newSetting;
        }

        return $this->render('editSettings.html', $allSettings);
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
        foreach ($options as $key => $row) {
            $newArray[$row->value] = ($row->name) ?: $row->value;
        }

        return $newArray;
    }

    /**
     * Save Settings
     *
     * Save all site configuration settings
     */
    public function saveSettings()
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $SettingMapper = $mapper('SettingMapper');
        $Json = $this->container->json;

        // Fetch custom theme settings from themes/<theme-name>/themeSettings.json
        $customSettings = $Json->getThemeSettings();

        // Get $_POST data array
        $allSettings = $this->request->getParsedBody();

        // Save each setting
        foreach ($allSettings['setting_key'] as $key => $row) {
            $setting = $SettingMapper->make();
            $setting->id = $allSettings['id'][$key];
            $setting->setting_value = $allSettings['setting_value'][$key];

            // If there is no ID, then this is a new custom setting to save
            // Import setting information from theme file
            if (empty($allSettings['id'][$key])) {
                // Get theme custom setting array key for this setting_key for reference
                $jsonKey = array_search($allSettings['setting_key'][$key], array_column($customSettings, 'key'));

                // Populate the new custom setting and save
                $setting->category = 'custom';
                $setting->sort_order = $customSettings[$jsonKey]->sort;
                $setting->setting_key = $customSettings[$jsonKey]->key;
                $setting->input_type = $customSettings[$jsonKey]->inputType;
                $setting->label = $customSettings[$jsonKey]->label;
                $setting->help = $customSettings[$jsonKey]->help;
            }

            $SettingMapper->save($setting);
        }

        // Redirect back to list of settings
        return $this->redirect('showSettings');
    }

    /**
     * Delete Custom Setting
     *
     * Delete single record
     * @param array
     */
    public function deleteCustomSettings($args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $SettingMapper = $mapper('SettingMapper');

        $setting = $SettingMapper->make();
        $setting->id = (int) $args['id'];
        $SettingMapper->delete($setting);

        // Redirect back to list of settings
        return $this->redirect('showSettings');
    }
}
