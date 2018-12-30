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
     * List all site configuration settings in bulk edit form
     */
    public function showSettings()
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $SettingMapper = $mapper('SettingMapper');

        // Fetch settings
        $settings = $SettingMapper->findSiteSettings();

        return $this->render('settings.html', $settings);
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

        $allSettings = $this->request->getParsedBody();

        // Save settings
        foreach ($allSettings['setting_key'] as $key => $row) {
            $setting = $SettingMapper->make();
            $setting->id = $allSettings['id'][$key];
            $setting->setting_value = $allSettings['setting_value'][$key];
            $SettingMapper->save($setting);
        }

        // Redirect back to list of settings
        return $this->redirect('showSettings');
    }

    /**
     * Edit Custom Setting
     *
     * Update or add custom setting
     * @param mixed $args Array with setting ID - Optional
     */
    public function editCustomSetting($args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $SettingMapper = $mapper('SettingMapper');

        // Fetch custom setting, or create new custom setting
        if (is_numeric($args['id'])) {
            // Existing
            $customSetting = $SettingMapper->findById($args['id']);
        } elseif (is_string($args['id'])) {
            // New
            $customSetting = $SettingMapper->make();
        }

        return $this->render('editCustomSetting.html', $customSetting);
    }

    /**
     * Save Custom Setting
     */
    public function saveCustomSetting()
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $SettingMapper = $mapper('SettingMapper');

        $setting = $SettingMapper->make();
        $setting->id = $this->request->getParsedBodyParam('id');
        $setting->category = 'custom';
        $setting->sort_order = $this->request->getParsedBodyParam('sort_order');
        $setting->setting_key = $this->request->getParsedBodyParam('setting_key');
        $setting->input_type = $this->request->getParsedBodyParam('input_type');
        $setting->label = $this->request->getParsedBodyParam('label');
        $setting->help = $this->request->getParsedBodyParam('help');
        $SettingMapper->save($setting);

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
