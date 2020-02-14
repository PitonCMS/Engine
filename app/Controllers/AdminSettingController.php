<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

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

        // Get saved settings from database
        $category = $args['cat'] ?? null;
        $savedSettings = $settingMapper->findSiteSettings($category) ?? [];

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

        // Get $_POST data array
        $post = $this->request->getParsedBody();

        // Save each setting
        foreach ($post['setting'] as $row) {
            $setting = $settingMapper->make();
            $setting->id = (int) $row['id'];

            // Check for a setting delete flag
            if (isset($row['delete'])) {
                $settingMapper->delete($setting);
                continue;
            }

            $setting->category = $row['category'];
            $setting->setting_key = $row['setting_key'];
            $setting->setting_value = $row['setting_value'];
            $settingMapper->save($setting);
        }

        // Redirect back to list of settings
        $routeCategory = $post['category'];
        return $this->redirect('adminSettings', ['cat' => $routeCategory]);
    }
}
