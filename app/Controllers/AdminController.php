<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2026 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

namespace Piton\Controllers;

use DOMDocument;
use Psr\Http\Message\ResponseInterface as Response;

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
        $pageMapper = ($this->container->get('dataMapper'))('PageMapper');
        $sitemapHandler = $this->container->get('sitemapHandler');

        // Get all published content
        $allContent = array_merge($pageMapper->findPublishedContent());
        $links = [];

        foreach ($allContent as $page) {
            $link = ($page->collection_slug) ? $page->collection_slug . '/' : null;
            $link .= ($page->page_slug === 'home') ? '' : $page->page_slug;

            $links[] = [
                'link' => $link,
                'date' => date('c', strtotime($page->updated_date)),
            ];
        }

        // Make sitemap
        $uri = $this->request->getUri();
        if ($sitemapHandler->make($links, $uri->getScheme() . '://' . $uri->getAuthority(), $this->settings['environment']['production'])) {
            if ($this->settings['environment']['production']) {
                $this->setAlert('info', 'Sitemap updated and search engines alerted', $sitemapHandler->getMessages());
            }
        } else {
            $this->setAlert('danger', 'Unable to update sitemap', $sitemapHandler->getMessages());
        }

        return $this->redirect('adminSitemap');
    }

    /**
     * Show Support Index
     *
     * @param array $args
     * @return Response
     */
    public function showSupportIndex($args): Response
    {
        $data['subject'] = $args['subject'];

        return $this->render("support/index.html", $data);
    }

    /**
     * Show Support Content Page
     *
     * @param array $args
     * @return Response
     */
    public function showSupportContent($args): Response
    {
        // Load dependencies
        $markdown = $this->container->get('markdownParser');

        // Pass through reference to subject
        $data['subject'] = $args['subject'];

        // Build path to file and add deep link to anchor
        $data['link'] = $args['link'] ?? null;
        $supportFile = ROOT_DIR . "vendor/pitoncms/engine/support/{$args['subject']}/{$args['file']}.md";

        // Send 404 if support file is not found
        if (!file_exists($supportFile)) {
            $this->notFound();
        }

        $supportContent = $markdown->convert(file_get_contents($supportFile));

        // Parse support HTML to add heading ID's for links, and build Table of Contents
        // Start with a DOMDocument
        $document = new \DOMDocument();
        $document->preserveWhiteSpace = false;

        // Because DOMDocument::loadHTML treats your string as being in ISO-8859-1, we are adding a XML Declaration UTF-8 hack
        $document->loadHTML('<?xml encoding="utf-8" ?>' . $supportContent);

        // Use DOMXPath to find headings, but skip h1's
        $xpath = new \DOMXPath($document);
        $nodes = $xpath->query("//h1 | //h2 | //h3 | //h4 | //h5 | //h6");

        // Start TOC list and loop through nodes
        $toc = '';

        /** @var \DOMElement $node */
        foreach ($nodes as $node) {
            // Add id to heading
            $id = str_replace(' ', '-', strtolower($node->nodeValue));
            $node->setAttribute('id', $id);

            // Add the TOC link
            $toc .= "<a href=\"#$id\" class=\"help-toc__{$node->nodeName}\">{$node->nodeValue}</a>\n";
        }

        // Get breadcrumb title from first H1 in file and render HTML
        $data['breadcrumbTitle'] = $document->getElementsByTagName('h1')[0]->textContent ?? 'Error';
        $data['supportContent'] = $document->saveHTML();
        $data['tableOfContents'] = $toc;

        return $this->render('support/supportFile.html', $data);
    }

    /**
     * Show Piton Engine aboutPiton Notes
     *
     * Used in Support > About
     * @param void
     * @return Response
     */
    public function aboutPiton(): Response
    {
        $markdown = $this->container->get('markdownParser');
        $log = $this->container->get('logger');

        // Get list of releases from GitHub. First check that cURL is installed on the server
        if (!function_exists('curl_init')) {
            // If curl is not installed on the server log info
            $log->info("Piton: cURL is not installed, unable to get releases from GitHub.");
        } else {
            // Get GitHub release history for engine
            // https://developer.github.com/v3/repos/releases
            $githubApi = 'https://api.github.com/repos/PitonCMS/Engine/releases';
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_URL => $githubApi,
                CURLOPT_USERAGENT => $this->request->getHeaderLine('HTTP_USER_AGENT'),
            ]);
            $responseBody = curl_exec($curl);
            $responseStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            // curl_close($curl); // No longer needed in PHP 8+

            // Verify that we have a response
            if ($responseStatus == '200') {
                $jsonReleases = json_decode($responseBody, true);
                $data['releases'] = array_slice($jsonReleases, 0, 3, true);

                // Format Markdown
                foreach ($data['releases'] as $key => $release) {
                    $data['releases'][$key]['body'] = $markdown->convert($release['body']);
                }

                // TODO
                // Check if there is a more current release available
                // if (array_search($installedRelease, array_column($releases, 'tag_name')) > 0) {
                //     $message = "The current PitonCMS version is {$releases[0]->tag_name}, you have version {$installedRelease}.";
                //     // $this->setAlert('info', 'There is a newer version of the PitonCMS Engine', $message);
                // }
            }
        }

        $data['breadcrumbTitle'] = 'About PitonCMS';
        // Not passing any supportContent through, but sending a flag to enable the breadcrumb
        $data['supportContent'] = true;

        return $this->render('support/about.html', $data);
    }
}
