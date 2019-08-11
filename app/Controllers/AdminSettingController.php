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
     * @param array $args
     */
    public function showSettings($args)
    {
        // Get dependencies
        $settingMapper = ($this->container->dataMapper)('SettingMapper');
        $definition = $this->container->definition;
        $session = $this->container->sessionHandler;

        // Get saved settings from session in case of reload, then database
        $category = $args['cat'] ?? null;
        $savedSettings =  $session->getFlashData('reloadData') ?? $settingMapper->findSiteSettings($category);

        // Get seeded PitonCMS settings definition
        if (null === $seededSettings = $definition->getSeededSiteSettings()) {
            throw new Exception('PitonCMS: Invalid seeded config/settings.json: ' . implode(', ', $definition->getErrorMessages()));
        }

        // Get custom settings definition
        if (null === $customSettings = $definition->getSiteSettings()) {
            $this->setAlert('danger', 'Custom Settings Error', $definition->getErrorMessages());
        } else {
            // Merge saved settings with custom settings
            $data['settings'] = $this->mergeSettings(
                $savedSettings,
                array_merge($seededSettings->settings, $customSettings->settings),
                $category
            );
        }

        // Set category flag in page to help with redirects
        $data['category'] = $category;

        return $this->render('tools/editSettings.html', $data);
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
        $validation = $this->container->validation;

        // Get $_POST data array
        $post = $this->request->getParsedBody();

        // Save each setting
        foreach ($post['setting'] as $row) {
            $setting = $settingMapper->make();
            $setting->id = $row['id'];

            // Check for a setting delete flag
            if (isset($row['delete'])) {
                $settingMapper->delete($setting);
                continue;
            }

            $setting->category = $row['category'];
            $setting->setting_key = $row['setting_key'];
            $setting->setting_value = $row['setting_value'];

            // Validate data
            if (!$validation->validate($setting, 'setting')) {
                // Failed. Set alert message, objectify post data array, and set in session flash data
                $this->setAlert('danger', 'Data Error', $validation->getErrorMessages());
                $session = $this->container->sessionHandler;
                array_walk($post['setting'], function(&$val, $key) { $val = (object) $val; } );
                $session->setFlashData('reloadData', $post['setting']);
                break;
            }

            $settingMapper->save($setting);
        }

        // Redirect back to list of settings
        $routeCategory = $post['category'];
        return $this->redirect('adminSettings', ['cat' => $routeCategory]);
    }
}
