<?php
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */
namespace Piton\Library\Handlers;

use \Exception;

/**
 * Piton File Upload Handler
 *
 * Manages file uploads
 */
class FileUploadHandler
{
    /**
     * Uploaded Files Array
     * @var array
     */
    protected $uploadedFiles;

    /**
     * File Uploads Root Path
     * @var string
     */
    protected $uploadedFilesRoot;

    /**
     * New File Name
     * @var string
     */
    protected $fileName = '';

    /**
     * PHP Upload Error Code
     * @var int
     */
    protected $error = UPLOAD_ERR_OK;

    /**
     * Constructor
     *
     * @param array $uploadedfiles Array of Slim\Http\UploadedFile objects
     */
    public function __construct(array $uploadedFiles)
    {
        $this->uploadedFiles = $uploadedFiles;
        $this->uploadedFilesRoot = ROOT_DIR . 'public/media/';
    }

    /**
     * Upload File Action
     *
     * Upload file from $_FILES array
     * @param  string  $fileKey Array key for file
     * @return boolean          True|False
     */
    public function upload(string $fileKey)
    {
        if (!isset($this->uploadedFiles[$fileKey])) {
            throw new Exception('PitonCMS: File upload key does not exist');
        }

        $file = $this->uploadedFiles[$fileKey];

        if ($file->getError() === UPLOAD_ERR_OK) {
            // Get file name and extension
            $uploadFileName = $file->getClientFilename();
            $ext = strtolower(pathinfo($uploadFileName, PATHINFO_EXTENSION));

            // Create new file name and directory and ensure it is unique
            do {
                $name = $this->generateName();
                $path = $this->getFilePath($name);
                $exists = $this->makeFileDirectory($path);
            } while (!$exists);

            $this->fileName = "$name.$ext";
            $file->moveTo("{$this->uploadedFilesRoot}{$path}{$this->fileName}");

            unset($file);

            return true;
        }

        // Save error code
        $this->error = $file->getError();

        return false;
    }

    /**
     * Get New File Name
     *
     * @param  void
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Get File Path
     *
     * Derive file path based on file name
     * Path: fi/filename/
     * @param  string $fileName
     * @return string
     */
    public function getFilePath($fileName)
    {
        $directory = pathinfo($fileName, PATHINFO_FILENAME);
        $dir = substr($directory, 0, 2);

        return "$dir/$directory/";
    }

    public function getErrorMessage()
    {
        switch ($this->error) {
            case UPLOAD_ERR_INI_SIZE:
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = "Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = "File upload stopped by extension";
                break;

            default:
                $message = "Unknown upload error";
                break;
        }

        return $message;
    }

    /**
     * Make Directory Path
     *
     * Creates the directory path
     * @param  string $directoryPath
     * @return bool                  True|False
     */
    protected function makeFileDirectory($directoryPath)
    {
        $filePath = $this->uploadedFilesRoot . $directoryPath;

        // Create the path if the directory does not exist
        if (!is_dir($filePath)) {
            try {
                mkdir($filePath, 0775, true);
                return true;
            } catch (Exception $e) {
                throw new Exception('PitonCMS: Failed to create file upload directory');
            }
        }

        // The directory already exists
        return false;
    }

    /**
     * Generate Filename
     *
     * Generates new filename
     * @param  void
     * @return string
     */
    protected function generateName()
    {
        return bin2hex(random_bytes(6));
    }
}
