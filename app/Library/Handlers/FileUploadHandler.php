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
use \Closure;

/**
 * Piton Media File Upload Handler
 *
 * Manages Media file uploads
 */
class FileUploadHandler
{
    /**
     * Uploaded Files Array
     * @var array
     */
    protected $uploadedFiles;

    /**
     * Public Root Directory
     * @var string
     */
    protected $publicRoot;

    /**
     * New File Name
     * @var string
     */
    protected $filename;

    /**
     * Extension
     * @var string
     */
    protected $extension;

    /**
     * Media File Width
     * @var int
     */
    public $width;

    /**
     * Media File Height
     * @var int
     */
    public $height;

    /**
     * Media File URI Closure
     * @var closure
     */
    protected $mediaUriClosure;

    /**
     * PHP Upload Error Code
     * @var int
     */
    protected $error = UPLOAD_ERR_OK;

    /**
     * Constructor
     *
     * @param  array   $uploadedfiles   Array of Slim\Http\UploadedFile objects
     * @param  closure $mediaUriClosure Function to derive media file URI
     * @return void
     */
    public function __construct(array $uploadedFiles, closure $mediaUriClosure)
    {
        $this->uploadedFiles = $uploadedFiles;
        $this->publicRoot = ROOT_DIR . 'public';
        $this->mediaUriClosure = $mediaUriClosure;
    }

    /**
     * Upload Media File
     *
     * Upload file from $_FILES array
     * @param  string  $fileKey Array key for file upload
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
            $this->extension = mb_strtolower(pathinfo($uploadFileName, PATHINFO_EXTENSION));

            // Create new file name and directory and ensure it is unique
            do {
                $this->makeFilename();
            } while (!$this->makeFileDirectory());

            $file->moveTo($this->getAbsoluteFilename());

            // Set file attributes
            list($this->width, $this->height) = getimagesize($this->getAbsoluteFilename());

            unset($this->uploadedFiles[$fileKey]);

            return true;
        }

        // Otherwise save error code
        $this->error = $file->getError();

        return false;
    }

    /**
     * Get File Name
     *
     * Returns filename plus extension
     * @param  void
     * @return string
     */
    public function getFilename()
    {
        return "{$this->filename}.{$this->extension}";
    }

    /**
     * Get Absolute Filename
     *
     * Returns absolute path to file with extension
     * @param  void
     * @return string
     */
    public function getAbsoluteFilename()
    {
        return $this->publicRoot . $this->getFileUri() . $this->getFilename();
    }

    /**
     * Get File URI
     *
     * Derive file URI based on file name
     * @param  void
     * @return string
     */
    public function getFileUri()
    {
        return ($this->mediaUriClosure)($this->filename);
    }

    /**
     * Make Directory Path
     *
     * Creates the directory path
     * @return bool
     */
    protected function makeFileDirectory()
    {
        $filePath = $this->publicRoot . $this->getFileUri();

        // Create the path if the directory does not exist
        if (!is_dir($filePath)) {
            try {
                mkdir($filePath, 0775, true);
                return true;
            } catch (Exception $e) {
                throw new Exception('PitonCMS: Failed to create file upload directory. ' . $e->getMessage());
            }
        }

        // The directory already exists
        return false;
    }

    /**
     * Make Filename
     *
     * Generates new filename
     * @param  void
     * @return void
     */
    protected function makeFilename()
    {
        $this->filename = bin2hex(random_bytes(6));
    }

    /**
     * Get Upload Error Message
     *
     * Converts PHP UPLOAD_ERR_* codes to text
     * @param  void
     * @return string
     */
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
        }

        return $message;
    }
}
