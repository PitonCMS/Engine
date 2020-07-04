<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

namespace Piton\Models;

use Piton\ORM\DataMapperAbstract;

/**
 * Piton Data Store Mapper
 *
 * Stores key-value pairs
 */
class DataStoreMapper extends DataMapperAbstract
{
    protected $inCategories = "('site','contact','social','piton')";
    protected $table = 'data_store';
    protected $modifiableColumns = ['category', 'page_id', 'setting_key', 'setting_value'];

    /**
     * Find Settings
     *
     * Find all settings, or a category of settings
     * @param  $category site|contact|social|piton
     * @return array|null
     */
    public function findSiteSettings(string $category = null): ?array
    {
        $this->makeSelect();
        if (null === $category) {
            $this->sql .= ' and category in ' . $this->inCategories;
        } else {
            $this->sql .= ' and category = ?';
            $this->bindValues[] = $category;
        }

        return $this->find();
    }

    /**
     * Find Page Settings
     *
     * Get page level settings
     * @param  int   $pageId  Page ID
     * @return array|null
     */
    public function findPageSettings(int $pageId): ?array
    {
        $this->makeSelect();
        $this->sql .= ' and category = \'page\' and page_id = ?';
        $this->bindValues[] = $pageId;

        return $this->find();
    }

    /**
     * Set Application Alert
     *
     * Saves Piton alert notices for display in application that are not saved to session flash data.
     * Background scripts and processes should use this for messaging
     * @param string        $severity Severity level color code
     * @param string        $heading  Heading text
     * @param string|array  $messge   Message or array of messages (Optional)
     */
    public function setAppAlert(string $severity, string $heading, $message = null): void
    {
        // Get any existing alert messages
        $this->makeSelect();
        $this->sql .= " and `category` = 'piton' and `setting_key` = 'appAlert';";
        $data = $this->findRow();

        // Decode to array
        $alerts = is_string($data->setting_value) ? json_decode($data->setting_value, true) : [];

        // Append new alert to array
        $alerts[] = [
            'severity' => $severity,
            'heading' => $heading,
            'message' => (is_array($message)) ? $message : [$message]
        ];

        // Save alert messages
        $this->sql = "update {$this->table} set `setting_value` = ? where `category` = 'piton' and `setting_key` = 'appAlert';";
        $this->bindValues[] = json_encode($alerts);
        $this->execute();
    }

    /**
     * Unset Application Alert
     *
     * Removes saved application alert notices
     * @param void
     * @return void
     */
    public function unsetAppAlert(): void
    {
        $this->sql = "update {$this->table} set `setting_value` = null where `category` = 'piton' and `setting_key` = 'appAlert';";
        $this->execute();
    }
}
