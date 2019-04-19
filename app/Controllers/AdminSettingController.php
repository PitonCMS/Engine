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
        $settingMapper = ($this->container->dataMapper)('SettingMapper');
        $json = $this->container->json;

        // Fetch settings from database
        $allSettings = $settingMapper->findSiteSettings();

        // Fetch custom settings
        $jsonFilePath = ROOT_DIR . "structure/definitions/customSettings.json";
        if (null === $customSettings = $json->getJson($jsonFilePath, 'setting')) {
            $this->setAlert('danger', 'Custom Settings Error', $json->getErrorMessages());
        } else {
            // Merge saved settings with custom settings
            $allSettings = $this->mergeSettings($allSettings, $customSettings->settings);
        }

        return $this->render('editSettings.html', $allSettings);
    }

    /**
     * Save Settings
     *
     * Save all site configuration settings
     */
    public function saveSettings()
    {
        // Get dependencies
        $settingMapper = ($this->container->dataMapper)('SettingMapper');
        $json = $this->container->json;

        // Fetch custom settings
        $jsonFilePath = ROOT_DIR . "structure/definitions/customSettings.json";
        $customSettings = $json->getJson($jsonFilePath, 'setting');
        $customSettings = $customSettings->settings;

        // Get $_POST data array
        $allSettings = $this->request->getParsedBody();

        // Save each setting
        foreach ($allSettings['setting_key'] as $key => $row) {
            $setting = $settingMapper->make();
            $setting->id = $allSettings['setting_id'][$key];

            // Check for a custom setting delete
            if (isset($allSettings['setting_delete'][$key])) {
                $settingMapper->delete($setting);
                continue;
            }

            $setting->setting_value = $allSettings['setting_value'][$key];

            // If there is no ID, then this is a new custom setting to save
            // Import setting information from custom file
            if (empty($allSettings['setting_id'][$key])) {
                // Get custom setting array key for this setting_key for reference
                $jsonKey = array_search($allSettings['setting_key'][$key], array_column($customSettings, 'key'));

                // Populate the new custom setting and save
                $setting->category = 'custom';
                $setting->sort_order = $customSettings[$jsonKey]->sort;
                $setting->setting_key = $customSettings[$jsonKey]->key;
                $setting->input_type = $customSettings[$jsonKey]->inputType;
                $setting->label = $customSettings[$jsonKey]->label;
                $setting->help = $customSettings[$jsonKey]->help;
            }

            $settingMapper->save($setting);
        }

        // Redirect back to list of settings
        return $this->redirect('adminSettings');
    }
}
