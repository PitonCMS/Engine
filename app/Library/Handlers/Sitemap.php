<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

namespace Piton\Library\Handlers;

use Exception;

/**
 * Piton Sitemap Handler
 */
class Sitemap
{
    protected $sitemapFileName = 'sitemap.xml';
    protected $sitemapFilePath;
    protected $domain;
    protected $logger;
    protected $sitemapXML;
    protected $messages = [];

    /**
     *  Constructor
     *
     * @param  object $logger   Logging object
     * @return void
     */
    public function __construct(object $logger)
    {
        $this->logger = $logger;
        $this->sitemapFilePath = ROOT_DIR . 'public/' . $this->sitemapFileName;
    }

    /**
     * Generate sitemap
     *
     * Renders XML, writes to file, and pings search engines
     * @param  array   $links
     * @param  string  $domain             Fully qualified domain name with scheme (http/s)
     * @param  bool $alertSearchEngines Set true in production to ping search engines
     * @return bool
     */
    public function make(array $links = null, string $domain, bool $alertSearchEngines = null): bool
    {
        $this->logger->notice('PitonCMS: Making sitemap');

        if (empty($links)) {
            $this->messages[] = 'No content links to create sitemap.';
            $this->logger->notice('..No links provided for sitemap');
            return null;
        }

        $this->domain = $domain;
        $this->generateXML($links);
        $status = $this->writeXMLFile();

        // Only alert search engines if in production
        if ($status && $alertSearchEngines) {
            $this->logger->notice('..Alerting search engines with updated sitemap');
            $this->alertSearchEngines();
        }

        return $status;
    }

    /**
     * Generate XML
     *
     * Creates XML string from array of links
     * @param  array  $links  Array of links
     * @return void
     */
    public function generateXML(array $links): void
    {
        $this->logger->notice('..Generating XML');

        // Start sitemap XML header
        $this->sitemapXML = "<\x3Fxml version=\"1.0\" encoding=\"UTF-8\"\x3F>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

        // Add all links
        foreach ($links as $link) {
            $this->sitemapXML .= "\t<url>\n\t\t<loc>{$this->domain}/{$link['link']}</loc>\n";
            $this->sitemapXML .= "\t\t<lastmod>{$link['date']}</lastmod>\n \t</url>\n";
        }

        // Close the sitemap XML string
        $this->sitemapXML .= "</urlset>\n";
    }

    /**
     * Write XML File
     *
     * @param  void
     * @return bool
     */
    public function writeXMLFile(): bool
    {
        // Write the sitemap data to file
        $this->logger->notice('..Writing data to file');

        try {
            $fh = fopen($this->sitemapFilePath, 'w');
            fwrite($fh, $this->sitemapXML);
            fclose($fh);

            return true;
        } catch (Exception $e) {
            // Log failure
            $this->logger->error('..Failed to write sitemap');
            $this->logger->error(print_r($e->getMessage(), true));
            $this->messages[] = 'Failed to write sitemap: ' . print_r($e->getMessage(), true);

            return false;
        }
    }

    /**
     * Alert Search Engines
     *
     * Inform Google and Bing that there is a new sitemap
     * @param void
     * @return void
     */
    public function alertSearchEngines(): void
    {
        // Ping Google and Bing with the updated sitemap
        $sitemapUrl = $this->domain . '/' . $this->sitemapFileName;

        // Google
        $pingRequests[] = "http://www.google.com/ping?sitemap=" . $sitemapUrl;

        // Bing
        $pingRequests[] = 'http://www.bing.com/ping?sitemap=' . $sitemapUrl;

        foreach ($pingRequests as $searchEngine) {
            $logMessage = '..Submitting sitemap to: ' . $searchEngine;

            try {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_URL, $searchEngine);
                curl_exec($ch);
                $httpResponseStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                // Save messages
                $this->logger->notice($logMessage . ' Return status: ' . $httpResponseStatus);
                $this->messages[] = "Search Engine: $searchEngine, Return Status: $httpResponseStatus";
            } catch (Exception $e) {
                // Log failure
                $this->logger->error('PitonCMS: Failed to connect to search engines ' . print_r($e->getMessage(), true));
                $this->messages[] = "Search Engine: $searchEngine, Return Status: $httpResponseStatus";
            }
        }

        $this->logger->notice('..Sitemap update complete');
    }

    /**
     * Get Messages
     *
     * Returns any messages generated from creating sitemap
     * @param  void
     * @return array|null
     */
    public function getMessages(): ?array
    {
        return $this->messages;
    }
}
