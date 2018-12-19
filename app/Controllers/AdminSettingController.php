<?php
/**
 * Admin Setting Controller
 *
 * Manage site level application settings
 */
namespace Piton\Controllers;

class AdminSettingController extends AdminBaseController
{
    /**
     * Show Settings
     *
     * List all site configuration settings
     */
    public function showSettings($args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $SettingMapper = $mapper('SettingMapper');

        // Fetch settings
        $settings = $SettingMapper->find();

        return $this->render('showSettings.html', ['settings' => $settings]);
    }

    /**
     * Save Settings
     *
     * Save all site configuration settings
     */
    public function saveSettings($args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $SettingMapper = $mapper('SettingMapper');

        $allSettings = $this->request->getParsedBodyParam('setting');

        // Save settings
        foreach ($allSettings['id'] as $key => $row) {
            $setting = $SettingMapper->make();
            $setting->id = $allSettings['id'][$key];
            $setting->setting_value = $allSettings['setting_value'][$key];
            $SettingMapper->save($setting);

            // TODO If changing themes, copy assets folder to public/theme
        }

        // Redirect back to list of settings
        return $this->redirect('showSettings');
    }
}
