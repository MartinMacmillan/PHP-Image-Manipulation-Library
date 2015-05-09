<?php

/**
 * Class ImageManipulation
 *
 * An image manipulation class for cropping, and other editing tasks.
 *
 * Note: I considered defaulting the destination output to overwrite the original, if not supplied;
 * but decided against it to prevent any "accidents" to source files.
 */

class ImageManipulation
{
    protected $imageSource;
    protected static $supportedFileTypes = array('jpg', 'jpeg', 'png');

    public function __construct($imageSource)
    {
        $this->imageSource = $imageSource;
    }

    /**
     * Getter for supported file types array.
     *
     * @return array
     */
    public function getSupportedFileTypes()
    {
        return self::$supportedFileTypes;
    }

    /**
     * Gets the file extension of the supplied image.
     * (We are looking for JPEGs and PNGs, specifically.)
     *
     * @return string
     */
    public function getFileExtension()
    {
        $fileExtension = explode('.', $this->imageSource);

        if (is_array($fileExtension) && count($fileExtension) > 1) {
            return end($fileExtension);
        }

        return false;
    }

    /**
     * Copies a source image, crops it to the specified dimensions and quality, then saves it to the requested destination.
     * This is an alias for protected methods cropFromJpeg and cropFromPng.
     * Uses PHP's Graphic Draw 2 library.
     *
     * @param string $newImageDestination
     * @param string $thumbnailSize (square)
     * @param string $desiredQuality: 0-100 (worst to best) for JPEG; 0-9 (best to worst) for PNG.
     * @return image
     */
    public function crop($newImageDestination, $thumbnailSize, $desiredQuality)
    {
        $fileExtension = $this->getFileExtension();

        $supportedFileTypes = $this->getSupportedFileTypes();

        if (!in_array($fileExtension, $supportedFileTypes)) {
            return false;
        }

        if (is_file($this->imageSource)) {

            if ($fileExtension === 'jpg' || $fileExtension === 'jpeg') {
                $croppedImage = $this->cropFromJpeg($newImageDestination, $thumbnailSize, $desiredQuality);
            }

            if ($fileExtension === 'png') {
                $croppedImage = $this->cropFromPng($newImageDestination, $thumbnailSize, $desiredQuality);
            }

            return $croppedImage;

        }

        return false;
    }

    /**
     * Creates a new JPEG image to the inputted specifications.
     * (This is NOT directly callable, as we perform checks first.)
     *
     * @param string $newImageDestination
     * @param integer $thumbnailSize
     * @param string $newImageDestination
     * @param integer $desiredQuality
     * @return bool
     */
    protected function cropFromJpeg($newImageDestination, $thumbnailSize = 250, $desiredQuality = 75)
    {
        $sourceImage = imagecreatefromjpeg($this->imageSource);
        $sourceImageWidth = imagesx($sourceImage);
        $sourceImageHeight = imagesy($sourceImage);

        if ($sourceImageWidth > $sourceImageHeight) {
            $smallestSide = $sourceImageHeight;
        }

        if ($sourceImageHeight >= $sourceImageWidth) {
            $smallestSide = $sourceImageWidth;
        }

        $virtualImage = imagecreatetruecolor($thumbnailSize, $thumbnailSize);
        imagecopyresampled($virtualImage, $sourceImage, 0, 0, 0, 0, $thumbnailSize, $thumbnailSize, $smallestSide, $smallestSide);

        $croppedImage = imagejpeg($virtualImage, $newImageDestination, $desiredQuality);

        imagedestroy($sourceImage);

        return $croppedImage;
    }

    /**
     * Creates a new PNG image to the inputted specifications.
     * (This is NOT directly callable, as we perform checks first.)
     *
     * @param string $newImageDestination
     * @param integer $thumbnailSize
     * @param integer $compressionLevel
     * @return bool
     */
    protected function cropFromPng($newImageDestination, $thumbnailSize = 250, $compressionLevel = 0)
    {
        $sourceImage = imagecreatefrompng($this->imageSource);
        $sourceImageWidth = imagesx($sourceImage);
        $sourceImageHeight = imagesy($sourceImage);

        if ($sourceImageWidth >= $sourceImageHeight) {
            $smallestSide = $sourceImageHeight;
        }

        if ($sourceImageHeight > $sourceImageWidth) {
            $smallestSide = $sourceImageWidth;
        }

        $virtualImage = imagecreatetruecolor($thumbnailSize, $thumbnailSize);
        imagealphablending($virtualImage, false);
        imagesavealpha($virtualImage, true);

        imagecopyresampled($virtualImage, $sourceImage, 0, 0, 0, 0, $thumbnailSize, $thumbnailSize, $smallestSide, $smallestSide);

        $croppedImage = imagepng($virtualImage, $newImageDestination, $compressionLevel);

        imagedestroy($sourceImage);

        return $croppedImage;
    }

    /**
     * Converts a colour image to black and white (grayscale), then saves it to the requested destination.
     * This is an alias for protected methods jpegToBlackAndWhite and pngToBlackAndWhite.
     * Uses PHP's Graphic Draw 2 library.
     * Note that, occasionally, the results may not be as expected; this is purely down to the GD driver.
     *
     * @param $newImageDestination string
     * @return bool
     */
    public function blackAndWhite($newImageDestination)
    {
        $fileExtension = $this->getFileExtension();

        $supportedFileTypes = $this->getSupportedFileTypes();

        if (!in_array($fileExtension, $supportedFileTypes)) {
            return false;
        }

        if ($fileExtension === 'jpg' || $fileExtension === 'jpeg') {
            $blackAndWhiteImage = $this->jpegToBlackAndWhite($newImageDestination);
        }

        if ($fileExtension === 'png') {
            $blackAndWhiteImage = $this->pngToBlackAndWhite($newImageDestination);
        }

        return $blackAndWhiteImage;
    }


    /**
     * Converts a PNG image to a grayscale equivalent.
     * (This is NOT directly callable, as we perform checks first.)
     *
     * @param $newImageDestination string
     * @return bool
     */
    protected function jpegToBlackAndWhite($newImageDestination)
    {
        $sourceImage = imagecreatefromjpeg($this->imageSource);
        imagefilter($sourceImage, IMG_FILTER_GRAYSCALE);
        imagefilter($sourceImage, IMG_FILTER_CONTRAST, 202);

        $bnwImage = imagejpeg($sourceImage, $newImageDestination);

        imagedestroy($sourceImage);

        return $bnwImage;
    }

    /**
     * Converts a PNG image to a grayscale equivalent.
     * (This is NOT directly callable, as we perform checks first.)
     *
     * @param $newImageDestination string
     * @return bool
     */
    protected function pngToBlackAndWhite($newImageDestination)
    {
        $sourceImage = imagecreatefrompng($this->imageSource);
        imagefilter($sourceImage, IMG_FILTER_GRAYSCALE);
        imagefilter($sourceImage, IMG_FILTER_CONTRAST, 202);

        $bnwImage = imagepng($sourceImage, $newImageDestination);

        imagedestroy($sourceImage);

        return $bnwImage;
    }
}
