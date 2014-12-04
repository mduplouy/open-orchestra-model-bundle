<?php

namespace PHPOrchestra\Media\Manager;

use Imagick;
use PHPOrchestra\MediaBundle\Model\MediaInterface;

/**
 * Class ImageResizerManager
 */
class ImageResizerManager
{
    protected $uploadDir;
    protected $formats;

    /**
     * @param string $uploadDir
     * @param array  $formats
     */
    public function __construct($uploadDir, array $formats)
    {
        $this->uploadDir = $uploadDir;
        $this->formats = $formats;
    }

    /**
     * @param MediaInterface $media
     */
    public function generateAllThumbnails(MediaInterface $media)
    {
        foreach ($this->formats as $key => $format) {
            $image = new Imagick($this->uploadDir . '/' . $media->getFilesystemName());
            $this->resizeImage($format, $image);

            $this->saveImage($media, $image, $key);
        }
    }

    /**
     * @param MediaInterface $media
     * @param int            $x
     * @param int            $y
     * @param int            $h
     * @param int            $w
     * @param string         $format
     */
    public function crop(MediaInterface $media, $x, $y, $h, $w, $format)
    {
        $image = new Imagick($this->uploadDir . '/' . $media->getFilesystemName());
        $image->cropImage($w, $h, $x, $y);
        $this->resizeImage($this->formats[$format], $image);

        $this->saveImage($media, $image, $format);
    }

    /**
     * @param MediaInterface $media
     * @param Imagick        $image
     * @param string         $key
     */
    protected function saveImage(MediaInterface $media, Imagick $image, $key)
    {
        $image->setImageCompression(Imagick::COMPRESSION_JPEG);
        $image->setImageCompressionQuality(75);
        $image->stripImage();
        $image->writeImage($this->uploadDir . '/' . $key . '-' . $media->getFilesystemName());
    }

    /**
     * @param $format
     * @param $image
     */
    protected function resizeImage($format, $image)
    {
        if (array_key_exists('width', $format) && array_key_exists('height', $format)) {
            $image->thumbnailImage($format['width'], $format['height']);
        } elseif (array_key_exists('max_height', $format)) {
            $image->resizeImage(0, $format['max_height'], Imagick::FILTER_LANCZOS, 1);
        } elseif (array_key_exists('max_width', $format)) {
            $image->resizeImage($format['max_width'], 0, Imagick::FILTER_LANCZOS, 1);
        }
    }
}