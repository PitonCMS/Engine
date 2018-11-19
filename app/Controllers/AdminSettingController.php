<?php
/**
 * Admin Setting Controller
 *
 * Manage site level application settings
 */
namespace Piton\Controllers;

class AdminSettingController extends BaseController
{
    /**
     * Show Settings
     *
     * List all site configuration settings
     */
    public function showSettings($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $SettingMapper = $mapper('SettingMapper');

        // Fetch settings
        $settings = $SettingMapper->find();

        return $this->container->view->render($response, '@admin/showSettings.html', ['settings' => $settings]);
    }

    /**
     * Save Settings
     *
     * Save all site configuration settings
     */
    public function saveSettings($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $SettingMapper = $mapper('SettingMapper');

        $allSettings = $request->getParsedBodyParam('setting');

        // Save settings
        foreach ($allSettings['id'] as $key => $row) {
            $setting = $SettingMapper->make();
            $setting->id = $allSettings['id'][$key];
            $setting->setting_value = $allSettings['setting_value'][$key];
            $SettingMapper->save($setting);

            // TODO If changing themes, copy assets folder to public/theme
        }

        // Redirect back to list of settings
        return $response->withRedirect($this->container->router->pathFor('showSettings'));
    }
}
