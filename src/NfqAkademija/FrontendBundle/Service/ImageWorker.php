<?php
namespace NfqAkademija\FrontendBundle\Service;

/**
 * Handle image manipulation
 *
 * Class ImageWorker
 * @package NfqAkademija\FrontendBundle\Service
 */
class ImageWorker
{
    /**
     * @var \Imagick
     */
    private $image;

    /**
     * $file has to be an absolute path to image file with file name and extension
     * @param string $file
     */
    public function __construct($file)
    {
        $this->image = new \Imagick($file);
    }

    /**
     * Convert image to png
     */
    public function convertToPng()
    {
        $this->image->setImageFormat('png');
    }

    /**
     * Save image to png format
     * @param string $name - file name
     * @param string $path - absolute path where to save image
     */
    public function saveImagePng($name, $path)
    {
        $this->image->writeImages(rtrim($path, '/').'/'.$name.".png", true);
    }

    /**
     * Create thumbnail image
     * @param $name
     * @param $path
     */
    public function createTempImage($name, $path)
    {
        $this->image->setImageFormat('png');
        $this->image->thumbnailImage(320, 240, true);
        $this->image->writeImages(rtrim($path, '/').'/thumb/'.$name.".png", true);
        $this->image->destroy();
    }
}
