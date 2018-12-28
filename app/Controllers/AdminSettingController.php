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

        return $this->render('showSettings.html', ['settings' => $settings]);
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

    /**
     * Manage Custom Settings
     *
     * Load all custom settings to edit, delete, or add
     * @param void
     */
    public function showCustomSettings()
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $SettingMapper = $mapper('SettingMapper');

        $customSettings = $SettingMapper->findCustomSettings();

        return $this->render('editCustomSettings.html', ['settings' => $customSettings]);
    }

    /**
     * Load New Custom Setting
     *
     * Responds as XHR request
     * @param void
     */
    public function newCustomSettingForm()
    {
        // If not a XHR request respond as 404
        if (!$this->request->isXhr()) {
            $this->notFound();
        }

        $template = "{% import '@admin/editCustomSettingsFormMacro.html' as settingForm %}";
        $template .= " {% set setting = setting|merge({'key': uniqueKey()}) %} {{ settingForm.custom( setting ) }}";
        $settingFormHtml = $this->container->view->fetchFromString($template, ['setting' => []]);

        // Set the response type
        $r = $this->response->withHeader('Content-Type', 'application/json');

        return $r->write(json_encode(["html" => $settingFormHtml]));
    }

    /**
     * Save Custom Settings
     */
    public function saveCustomSettings()
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $SettingMapper = $mapper('SettingMapper');

        foreach ($this->request->getParsedBodyParam('setting_key') as $key => $value) {
            $setting = $SettingMapper->make();
            $setting->id = $this->request->getParsedBodyParam('id')[$key];
            $setting->category = 'custom';
            $setting->sort_order = $this->request->getParsedBodyParam('sort_order')[$key];
            $setting->setting_key = $this->request->getParsedBodyParam('setting_key')[$key];
            $setting->input_type = $this->request->getParsedBodyParam('input_type')[$key];
            $setting->label = $this->request->getParsedBodyParam('label')[$key];
            $setting->help = $this->request->getParsedBodyParam('help')[$key];
            $SettingMapper->save($setting);
        }

        // Redirect back to list of settings
        return $this->redirect('showCustomSettings');
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

        if (!$this->request->isXhr()) {
            // Redirect back to list of settings
            return $this->redirect('showCustomSettings');
        }

        // Set the response type
        $r = $this->response->withHeader('Content-Type', 'application/json');

        return $r->write(json_encode(["status" => "success"]));
    }
}
