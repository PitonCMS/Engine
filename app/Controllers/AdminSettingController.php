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

        // Fetch theme settings from themes/<theme-name>/themeSettings.json
        if (null === $themeSettings = $Json->getThemeSettings()) {
            $this->setAlert('danger', 'Theme Settings Error', $Json->getErrorMessages());
        } else {
            // Move up multi-dimensional array one key
            $themeSettings = $themeSettings->settings;
        }

        // Create union of settings from DB and from JSON file by matching keys
        foreach ($allSettings as $allKey => $setting) {
            // Skip ahead if non-theme setting
            if ($setting->category !== 'theme') {
                continue;
            }

            // Now see if we have a theme setting from JSON that matches one in the DB
            foreach ($themeSettings as $settingKey => $theme) {
                if ($setting->setting_key === $theme->key) {
                    // There is a match on setting key so update display properties
                    // use themeSettings.json as the master reference
                    $setting->sort_order = $theme->sort;
                    $setting->label = $theme->label;
                    $setting->input_type = $theme->inputType;
                    $setting->help = $theme->help;

                    // Include select options array
                    if ($theme->inputType === 'select') {
                        $setting->options = $this->createOptionsArray($theme->options);
                    }

                    // Unset theme setting and skip to the next outer loop iteration
                    unset($themeSettings[$settingKey]);
                    continue 2;
                }
            }

            // Found an orphaned theme setting in the DB, so mark it as such for optional delete
            $allSettings[$allKey]->orphaned = true;

            if ($allSettings[$allKey]->input_type = 'select') {
                // For readability purposes, change orphaned selects to inputs
                $allSettings[$allKey]->input_type = 'input';
            }
        }

        // Any remaining $themeSettings are new and have not yet been saved to the DB
        // Append these to the settings array
        foreach ($themeSettings as $setting) {
            // Create setting object
            $newSetting = $SettingMapper->make();
            $newSetting->category = 'theme';
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
        foreach ($options as $row) {
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

        // Fetch theme settings from themes/<theme-name>/themeSettings.json
        $themeSettings = $Json->getThemeSettings();
        $themeSettings = $themeSettings->settings;

        // Get $_POST data array
        $allSettings = $this->request->getParsedBody();

        // Save each setting
        foreach ($allSettings['setting_key'] as $key => $row) {
            $setting = $SettingMapper->make();
            $setting->id = $allSettings['id'][$key];
            $setting->setting_value = $allSettings['setting_value'][$key];

            // If there is no ID, then this is a new theme setting to save
            // Import setting information from theme file
            if (empty($allSettings['id'][$key])) {
                // Get theme setting array key for this setting_key for reference
                $jsonKey = array_search($allSettings['setting_key'][$key], array_column($themeSettings, 'key'));

                // Populate the new theme setting and save
                $setting->category = 'theme';
                $setting->sort_order = $themeSettings[$jsonKey]->sort;
                $setting->setting_key = $themeSettings[$jsonKey]->key;
                $setting->input_type = $themeSettings[$jsonKey]->inputType;
                $setting->label = $themeSettings[$jsonKey]->label;
                $setting->help = $themeSettings[$jsonKey]->help;
            }

            $SettingMapper->save($setting);
        }

        // Redirect back to list of settings
        return $this->redirect('showSettings');
    }

    /**
     * Delete Theme Setting
     *
     * XHR Request
     */
    public function deleteThemeSetting()
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $SettingMapper = $mapper('SettingMapper');
        $status = 'error';

        if ($this->request->getParsedBodyParam('id')) {
            $setting = $SettingMapper->make();
            $setting->id = $this->request->getParsedBodyParam('id');
            $SettingMapper->delete($setting);

            $status = 'success';
        }

        // Set the response type
        $r = $this->response->withHeader('Content-Type', 'application/json');

        return $r->write(json_encode(["status" => $status]));
    }
}
