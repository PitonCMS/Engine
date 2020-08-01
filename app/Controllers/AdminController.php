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

use Slim\Http\Response;
use DOMDocument;

/**
 * Piton Admin Controller
 */
class AdminController extends AdminBaseController
{
    /**
     * Admin Home Page
     *
     * @param void
     * @param Response
     */
    public function home(): Response
    {
        return $this->render('home.html');
    }

    /**
     * Sitemap
     *
     * Shows current sitemap, and sitemap update submit button
     * @param void
     * @return Response
     */
    public function sitemap(): Response
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
     * @param void
     * @return Response
     */
    public function updateSitemap(): Response
    {
        // Get dependencies
        $pageMapper = ($this->container->dataMapper)('PageMapper');
        $sitemapHandler = $this->container->get('sitemapHandler');

        // Get all published content
        $allContent = array_merge($pageMapper->findPublishedContent());
        $links = [];

        foreach ($allContent as $page) {
            $link = ($page->collection_slug) ? $page->collection_slug . '/' : null;
            $link .= ($page->page_slug === 'home') ? '' : $page->page_slug;

            $links[] = [
                'link' => $link,
                'date' => date('c', strtotime($page->updated_date))
            ];
        }

        // Make sitemap
        if ($sitemapHandler->make($links, $this->request->getUri()->getBaseUrl(), $this->settings['environment']['production'])) {
            $this->setAlert('info', 'Sitemap updated and search engines alerted', $sitemapHandler->getMessages());
        } else {
            $this->setAlert('danger', 'Unable to update sitemap', $sitemapHandler->getMessages());
        }

        return $this->redirect('adminSitemap');
    }

    /**
     * Show Help Page
     *
     * @param array $args
     * @return Response
     */
    public function showHelp($args): Response
    {
        // Load dependencies
        $markdown = $this->container->markdownParser;

        // Check if a help file was requested, if not default to help index
        if (!isset($args['file'])) {
            return $this->render('help/help.html');
        }

        // If requesting the release notes page from GitHub
        if ($args['file'] === 'adminHelpDeveloperRelease') {
            return $this->release();
        }

        // Add deep link to anchor
        $data['link'] = $args['link'] ?? null;

        // Get subject from help file name. Client is default
        $data['subject'] = 'client';
        if (stripos($args['file'], 'designer') !== false) {
            $data['subject'] = 'designer';
        } elseif (stripos($args['file'], 'developer') !== false) {
            $data['subject'] = 'developer';
        }

        $helpFile = ROOT_DIR . "vendor/pitoncms/engine/templates/help/{$data['subject']}/{$args['file']}.md";

        if (file_exists($helpFile)) {
            $helpContent = $markdown->text(file_get_contents($helpFile));

            // Parse help file to modify headings
            $document = new DOMDocument();
            $document->loadHTML($helpContent);

            // Get heading tags h1..h6 and set ID so we can deep link to content
            foreach (['h1', 'h2', 'h3', 'h4', 'h5', 'h6'] as $h) {
                $nodes = $document->getElementsByTagName($h);
                foreach ($nodes as $node) {
                    $value = str_replace(' ', '-', strtolower($node->nodeValue));
                    $node->setAttribute('id', $value);
                }
            }

            $data['helpContent'] = $document->saveHTML();
        } else {
            $this->container->logger->warning("PitonCMS: Help file does not exist: Subject {$data['subject']}, File {$args['file']}.");
            $data['helpContent'] = "<h1>Help File {$args['file']} Does Not Exist</h1>";
        }

        return $this->render('help/help.html', $data);
    }

    /**
     * Show Piton Engine Release Notes
     *
     * Used in Help > Developer > Version
     * @param void
     * @return Response
     */
    public function release(): Response
    {
        $markdown = $this->container->markdownParser;
        // $installedRelease = $this->settings['environment']['engine'];
        $responseBody = '';

        if (!function_exists('curl_init')) {
            // If curl is not installed display notice
            $response = $this->response;
            $response->write(sprintf(
                "<html><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8'><title>" .
                "Piton Application Error</title><style>body{margin:0;padding:30px;font:14px / 1.5 Helvetica," .
                " Arial, Verdana, sans-serif; background-color:hsl(0, 0%%, 94%%);}h1{margin:0;font-size:48px;" .
                "font-weight:normal;line-height:48px;}strong{display:inline-block;width:75px;} .lead{ font-size:18px;}" .
                " .navbar{ position:static; top:0; right:0; left:0; background-color:#336699; color:#ffffff; " .
                "font-size:22.5; padding:.75rem; padding-left:30px; margin-top:-30px; margin-left:-30px; " .
                "margin-right:-30px; margin-bottom:20px;}</style></head><body><div class=\"navbar\">PitonCMS " .
                "</div>%s %s %s</body></html>",
                '<h1>
                    PHP cURL not installed
                </h1>',
                '<p class="lead">
                    Required module cURL to get GitHub release notes has not been enabled on this server.
                </p>',
                'Go to <a href="https://github.com/PitonCMS/Engine/releases" target="_blank">GitHub PitonCMS / Engine</a> to available releases.'
            ));
            return $response;
        } else {
            // Get GitHub release history for engine
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

                // TODO
                // Check if there is a more current release available
                // if (array_search($installedRelease, array_column($releases, 'tag_name')) > 0) {
                //     $message = "The current PitonCMS version is {$releases[0]->tag_name}, you have version {$installedRelease}.";
                //     // $this->setAlert('info', 'There is a newer version of the PitonCMS Engine', $message);
                // }
            } else {
                $releases = [];
            }
        }

        return $this->render('help/developer/adminHelpDeveloperRelease.html', ['releases' => $releases, 'subject' => 'developer']);
    }
}
