<?php
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */
namespace Piton\Library\Utilities;

use FilesystemIterator;

/**
 *  Piton Toolbox Class
 *
 *  Misc Helper Methods
 */
class Toolbox
{
    /**
     * Truncate HTML Text
     *
     * Accepts an HTML string, and returns just the unformatted text, truncated to the number of words
     * @param string $text Input HTML string
     * @param intger, $characters Number of characters to return
     * @return string
     */
    public function truncateHtmlText($text, $characters = 300)
    {
        // Clean up html tags and special characters
        $text = preg_replace('/<[^>]*>/', ' ', $text);
        $text = str_replace("\r", '', $text); // replace with empty space
        $text = str_replace("\n", ' ', $text); // replace with space
        $text = str_replace("\t", ' ', $text); // replace with space
        $text = preg_replace('/\s+/', ' ', $text); // remove multiple consecutive spaces
        $text = preg_replace('/^[\s]/', '', $text); // Remove leading space from excerpt
        $text = preg_replace('/[\s]$/', '', $text); // Remove trailing space from excerpt

        // If we are already within the limit, just return the text
        if (mb_strlen($text) <= $characters) {
            return $text;
        }

        // Truncate to character limit if longer than requested
        $text = substr($text, 0, $characters);

        // We don't want the string cut mid-word, so search for the last space and trim there
        $lastSpacePosition = strrpos($text, ' ');
        if (isset($lastSpacePosition)) {
            // Cut the text at this last word
            $text = substr($text, 0, $lastSpacePosition);
        }

        return $text;
    }

    /**
     * Clean URLs
     *
     * Replaces any non alphanumeric or space characters with dashes
     * @param string $string Unformatted URL
     * @return string
     */
    public function cleanUrl($string)
    {
        // First replace ampersands with the word 'and'
        $string = str_replace('&', 'and', $string);

        // Remove slashes
        $string = str_replace('/', '-', $string);

        // Strip out any single quotes
        $string = str_replace("'", '', $string);

        // Remove unwelcome characters, and replace with dashes
        $string = preg_replace('/[^a-zA-Z0-9]+/', '-', $string);

        // Lower case
        $string = mb_strtolower($string);

        // Finally remove and trailing dashes
        $string = trim($string, '-');

        return $string;
    }

    /**
     * Get Directory Files
     *
     * Scans a given directory, and returns a multi-dimension array of file names
     * Ignores '.' '..' and sub directories by default
     * $ignore accepts file names or regex patterns
     * @param  string $dirPath Path to directory to scan
     * @param  mixed  $ignore  String | Array
     * @return array
     */
    public function getDirectoryFiles($dirPath, $ignore = null)
    {
        $files = [];
        $pattern = '/^\..+'; // Ignore all dot files by default
        $splitCamelCase = '/(?<=[a-z])(?=[A-Z])|(?<=[A-Z])(?=[A-Z][a-z])/';

        if (is_string($ignore) && !empty($ignore)) {
            // If we have a valid string add it to the regex
            $pattern .= '|' . $ignore;
        } elseif (is_array($ignore)) {
            // If we have a non-empty array then add it to the regex
            $multiIgnores = implode('|', $ignore);
            $pattern .= empty($multiIgnores) ? '' : '|' . $multiIgnores;
        }

        $pattern .= '/'; // Close regex

        if (is_dir($dirPath)) {
            foreach (new FilesystemIterator($dirPath) as $dirObject) {
                if ($dirObject->isDir() || preg_match($pattern, $dirObject->getFilename())) {
                    continue;
                }

                $baseName = $dirObject->getBasename('.' . $dirObject->getExtension());
                $readableFileName = preg_replace($splitCamelCase, '$1 ', $baseName);
                $readableFileName = ucwords($readableFileName);

                $files[] = [
                    'filename' => $dirObject->getFilename(),
                    'basename' => $baseName,
                    'readname' => $readableFileName
                ];
            }
        }

        return $files;
    }
}
