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
        if (null === $definition = $json->getJson(ROOT_DIR . 'composer.lock')) {
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
        $markdown = $this->container->markdownParser;
        $responseBody = '';

        // If curl is not installed display alert
        if (!function_exists('curl_init')) {
            $this->setAlert('warning', 'Required PHP cURL not installed');
        } else {
            // https://developer.github.com/v3/repos/releases
            $githubApi = 'https://api.github.com/repos/PitonCMS/Engine/releases';
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_URL => $githubApi,
                CURLOPT_USERAGENT => $this->request->getHeaderLine('HTTP_USER_AGENT')
            ]);
            $responseBody = curl_exec($curl);
            $responseStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            // Verify that we have a response
            if ($responseStatus == '200') {
                $releases = json_decode($responseBody);
                $releases = array_slice($releases, 0, 5, true);

                // Format Markdown
                foreach ($releases as $key => $release) {
                    $releases[$key]->body = $markdown->text($release->body);
                }

                // Check if there is a more current release
                if (array_search($args['release'], array_column($releases, 'tag_name')) > 0) {
                    $message = "The current version is {$releases[0]->tag_name}, you have version {$args['release']}.";
                    $message .= "\nTo upgrade, from your project root run <code>composer update pitoncms/engine</code>";
                    $this->setAlert('info', 'There is a newer version of the PitonCMS Engine', $message);
                }
            } else {
                $releases = [];
                $this->setAlert('warning', "$responseStatus Response From GitHub", $responseBody);
            }
        }

        return $this->render('releaseNotes.html', ['releases' => $releases]);
    }

    /**
     * Sitemap
     *
     * Shows current sitemap, and sitemap update submit button
     */
    public function sitemap()
    {
        // Get current sitemap
        $pathToSitemap = ROOT_DIR . 'public/sitemap.xml';
        $data = [];
        if (file_exists($pathToSitemap)) {
            $data['sitemapXML'] = file_get_contents($pathToSitemap);
            $data['lasteUpdateDate'] = filemtime($pathToSitemap);
        }

        return $this->render('tools/sitemap.html', $data);
    }

    /**
     * Update Sitemap
     *
     * Generates sitemap to public/sitemap.xml
     */
    public function updateSitemap()
    {
        // Get dependencies
        $pageMapper = ($this->container->dataMapper)('PageMapper');
        $sitemapHandler = $this->container->get('sitemapHandler');

        // Get all published content
        $allContent = array_merge($pageMapper->findPages(), $pageMapper->findCollectionPages());
        $links = [];

        foreach ($allContent as $page) {
            $link = ($page->collection_slug) ? $page->collection_slug . '/' : null;
            $link .= ($page->page_slug === 'home') ? '' : $page->page_slug;

            $links[] = [
                'link' => urlencode($link),
                'date' => date('c', strtotime($page->updated_date))
            ];
        }

        // Make sitemap
        if (!$sitemapHandler->make($links, $this->request->getUri()->getBaseUrl(), $this->siteSettings['production'])) {
            $this->setAlert('danger', 'Unable to update sitemap', $sitemapHandler->getMessages());
        } elseif ($this->siteSettings['production']) {
            $this->setAlert('info', 'Sitemap updated and search engines alerted', $sitemapHandler->getMessages());
        }

        return $this->redirect('adminSitemap');
    }
}
