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
 * Piton Admin Controller
 */
class AdminController extends AdminBaseController
{
    /**
     * Admin Home Page
     *
     * @param void
     */
    public function home()
    {
        $json = $this->container->json;

        // Get Piton Engine version from composer.lock
        if (null === $definition = $json->getJson(ROOT_DIR . '/composer.lock')) {
            $this->setAlert('danger', 'Error Reading composer.lock', $json->getErrorMessages());
        }

        $engineKey = array_search('pitoncms/engine', array_column($definition->packages, 'name'));
        $engineVersion = $definition->packages[$engineKey]->version;

        return $this->render('home.html', ['pitonEngineVersion' => $engineVersion]);
    }

    /**
     * Show Piton Engine Release Notes
     *
     * @param array $args GET Segment array
     */
    public function release($args)
    {
        $json = $this->container->json;

        // https://developer.github.com/v3/repos/releases
        $releases = '';
        $githubApi = 'https://api.github.com/repos/PitonCMS/Engine/releases';
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $githubApi,
            CURLOPT_USERAGENT => $this->request->getHeaderLine('HTTP_USER_AGENT')
        ]);
        $releases = curl_exec($curl);
        curl_close($curl);

        // Verify that we have a response
        if (!empty($releases)) {
            $releases = json_decode($releases);
        }

        return $this->render('releaseNotes.html', ['releases' => $releases]);
    }
}
