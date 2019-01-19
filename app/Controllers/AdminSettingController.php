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
        $json = $this->container->json;

        // Fetch settings from database
        $allSettings = $SettingMapper->findSiteSettings();

        // Fetch theme settings
        $theme = $this->container->get('settings')['site']['theme'];
        $jsonFilePath = ROOT_DIR . "themes/{$theme}/definitions/themeSettings.json";
        if (null === $themeSettings = $json->getJson($jsonFilePath, 'setting')) {
            $this->setAlert('danger', 'Theme Settings Error', $json->getErrorMessages());
        }

        // Merge saved settings with theme settings
        $allSettings = $this->mergeSettingsWithJsonFields($allSettings, $themeSettings->settings, 'global');

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
        $mapper = $this->container->dataMapper;
        $SettingMapper = $mapper('SettingMapper');
        $json = $this->container->json;

        // Fetch theme settings
        $theme = $this->container->get('settings')['site']['theme'];
        $jsonFilePath = ROOT_DIR . "themes/{$theme}/definitions/themeSettings.json";
        $themeSettings = $json->getJson($jsonFilePath, 'setting');
        $themeSettings = $themeSettings->settings;

        // Get $_POST data array
        $allSettings = $this->request->getParsedBody();

        // Save each setting
        foreach ($allSettings['setting_key'] as $key => $row) {
            $setting = $SettingMapper->make();
            $setting->id = $allSettings['setting_id'][$key];
            $setting->setting_value = $allSettings['setting_value'][$key];

            // If there is no ID, then this is a new theme setting to save
            // Import setting information from theme file
            if (empty($allSettings['setting_id'][$key])) {
                // Get theme setting array key for this setting_key for reference
                $jsonKey = array_search($allSettings['setting_key'][$key], array_column($themeSettings, 'key'));

                // Populate the new theme setting and save
                $setting->scope = 'global';
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
