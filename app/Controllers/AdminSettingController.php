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

        // Get saved settings from database
        $savedSettings = $settingMapper->findSiteSettings();

        // Get seeded PitonCMS settings definition
        $seededSettingsPath = ROOT_DIR . 'vendor/pitoncms/engine/config/settings.json';
        if (null === $seededSettings = $json->getJson($seededSettingsPath, 'setting')) {
            throw new Exception('PitonCMS: Invalid seeded config/settings.json: ' . implode($json->getErrorMessages(), ','));
        }

        // Get custom settings definition
        $customSettingsPath = ROOT_DIR . 'structure/definitions/siteSettings.json';
        if (null === $customSettings = $json->getJson($customSettingsPath, 'setting')) {
            $this->setAlert('danger', 'Custom Settings Error', $json->getErrorMessages());
        }

        // Merge saved settings with custom settings
        $allSettings = $this->mergeSettings($savedSettings, array_merge($seededSettings->settings, $customSettings->settings));

        return $this->render('tools/editSettings.html', $allSettings);
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

        // Fetch settings definitions just so we can get the order of settings defined by the designer
        $settingDefinition = array_merge(
            $json->getJson(ROOT_DIR . 'vendor/pitoncms/engine/config/settings.json', 'setting')->settings,
            $json->getJson(ROOT_DIR . 'structure/definitions/siteSettings.json', 'setting')->settings
        );

        // Get $_POST data array
        $allSettings = $this->request->getParsedBody();

        // Save each setting
        $sort = 1;
        foreach ($allSettings['setting_key'] as $key => $row) {
            $setting = $settingMapper->make();
            $setting->id = $allSettings['setting_id'][$key];

            // Check for a setting delete flag
            if (isset($allSettings['setting_delete'][$key])) {
                $settingMapper->delete($setting);
                continue;
            }

            $setting->setting_value = $allSettings['setting_value'][$key];
            $setting->sort = $sort++;

            // If there is no ID, then this is a new setting to save
            if (empty($allSettings['setting_id'][$key])) {
                // Get custom setting array key for this setting_key for reference
                $jsonKey = array_search($allSettings['setting_key'][$key], array_column($settingDefinition, 'key'));

                // Populate the new custom setting and save
                $setting->category = $settingDefinition[$jsonKey]->category ?? 'page';
                $setting->setting_key = $settingDefinition[$jsonKey]->key;
            }

            $settingMapper->save($setting);
        }

        // Redirect back to list of settings
        return $this->redirect('adminSettings');
    }
}
