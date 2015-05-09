<?php

require_once __DIR__.'/../src/ImageManipulation.php';


/**
 * Class ImageManipulationTest
 *
 * You will need to ensure you have access to the Graphic Draw library,
 * either locally, or on your virtual machine, to successfully run these tests.
 */
class ImageManipulationTest extends PHPUnit_Framework_TestCase
{

    /**
     * Generates a mock JPEG image for testing purposes.
     *
     * @param $imageName
     * @return bool
     */
    public static function generateJpeg($imageName)
    {
        $mockImage = imagecreate(500, 500);
        imagecolorallocate($mockImage, 255, 100, 70);

        $jpegImage = imagejpeg($mockImage, $imageName);
        imagedestroy($mockImage);

        return $jpegImage;
    }

    /**
     * Generates a mock PNG image for testing purposes.
     *
     * @param $imageName
     * @return bool
     */
    public static function generatePng($imageName)
    {
        $mockImage = imagecreatetruecolor(500, 500);
        imagealphablending($mockImage, false);
        imagesavealpha($mockImage, true);
        imagecolorallocate($mockImage, 255, 100, 70);

        $pngImage = imagepng($mockImage, $imageName, 0);
        imagedestroy($mockImage);

        return $pngImage;
    }

    /**
     * Checks if the mock file exists on the system, and, if so, removes it.
     *
     * @param $files
     * @return void
     */
    public static function removeFileIfExists($files = [])
    {
        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }

    /**
     * @test
     */
    public function it_provides_methods_to_modify_an_image()
    {
        $imageSource = 'thisIsAnImage.jpg';

        new ImageManipulation($imageSource);
    }

    /**
     * @test
     */
    public function it_provides_supported_file_types_in_an_array()
    {
        $imageSource = 'thisIsAnImage.jpg';

        $myImage = new ImageManipulation($imageSource);

        $supportedFileTypes = $myImage->getSupportedFileTypes();

        $this->assertEquals(is_array($supportedFileTypes) && count($supportedFileTypes) > 0, true);
    }

    /**
     * @test
     */
    public function it_gets_a_file_extension_from_an_image_string()
    {
        $imageSource = 'thisIsAnImage.jpg';

        $myImage = new ImageManipulation($imageSource);

        $fileExtension = $myImage->getFileExtension();

        $this->assertEquals(
            is_string($fileExtension) &&
            strlen($fileExtension) > 1 &&
            substr($fileExtension, 0 === '.'),
            true
        );
    }

    /**
     * @test
     */
    public function it_crops_a_given_jpeg_image()
    {
        // We will create an actual mock image to test our image methods.
        $originalImageSource = __DIR__.'/thisIsAnImage.jpg';
        $croppedImageSource = __DIR__.'/thisIsAnImage-th.jpg';

        // Clean-up before
        self::removeFileIfExists([$originalImageSource, $croppedImageSource]);

        // Create a blank image; good enough for our purposes.
        self::generateJpeg($originalImageSource);

        $myImage = new ImageManipulation($originalImageSource);

        $myImage->crop($croppedImageSource, 250, 100);

        $output = imagecreatefromjpeg($croppedImageSource);

        $this->assertEquals(imagesx($output), 250);
        $this->assertEquals(imagesy($output), 250);

        imagedestroy($output);

        // Clean-up after
        self::removeFileIfExists([$originalImageSource, $croppedImageSource]);
    }

    /**
     * @test
     */
    public function it_crops_a_given_png_image()
    {
        // We will create an actual mock image to test our image methods.
        $originalImageSource = __DIR__.'/thisIsAnImage.png';
        $croppedImageSource = __DIR__.'/thisIsAnImage-th.png';

        // Clean-up before
        self::removeFileIfExists([$originalImageSource, $croppedImageSource]);

        // Create a blank image; good enough for our purposes.
        self::generatePng($originalImageSource);

        $myImage = new ImageManipulation($originalImageSource);
        $myImage->crop($croppedImageSource, 250, 0);

        $output = imagecreatefrompng($croppedImageSource);

        $this->assertEquals(imagesx($output), 250);
        $this->assertEquals(imagesy($output), 250);

        imagedestroy($output);

        // Clean-up after
        self::removeFileIfExists([$originalImageSource, $croppedImageSource]);
    }

    /**
     * @test
     */
    public function it_creates_a_black_and_white_image_from_jpeg()
    {
        // We will create an actual mock image to test our image methods.
        $originalImageSource = __DIR__.'/thisIsAnImage.jpg';
        $bnwImageSource = __DIR__.'/thisIsAnImage-bnw.jpg';

        // Clean-up before
        self::removeFileIfExists([$originalImageSource, $bnwImageSource]);

        // Create a blank image; good enough for our purposes.
        self::generateJpeg($originalImageSource);

        $myImage = new ImageManipulation($originalImageSource);

        $myImage->BlackAndWhite($bnwImageSource);

        $output = imagecreatefromjpeg($bnwImageSource);

        // Sample the colour in the middle of the image.
        $rgb = imagecolorat($output, (imagesx($output) / 2), (imagesy($output) / 2));

        // Determine the individual Red, Green, and Blue values.
        $red = ($rgb >> 16) & 0xFF;
        $green = ($rgb >> 8) & 0xFF;
        $blue = $rgb & 0xFF;

        // In grayscale, these 3 values should be the same.
        $this->assertEquals($red === $green && $green === $blue && $blue === $red, true);

        imagedestroy($output);

        // Clean-up after
        self::removeFileIfExists([$originalImageSource, $bnwImageSource]);
    }

    /**
     * @test
     */
    public function it_creates_a_black_and_white_image_from_png()
    {
        // We will create an actual mock image to test our image methods.
        $originalImageSource = __DIR__.'/thisIsAnImage.png';
        $bnwImageSource = __DIR__.'/thisIsAnImage-bnw.png';

        // Clean-up before
        self::removeFileIfExists([$originalImageSource, $bnwImageSource]);

        // Create a blank image; good enough for our purposes.
        self::generatePng($originalImageSource);

        $myImage = new ImageManipulation($originalImageSource);

        $myImage->BlackAndWhite($bnwImageSource);

        $output = imagecreatefrompng($bnwImageSource);

        // Sample the colour in the middle of the image.
        $rgb = imagecolorat($output, (imagesx($output) / 2), (imagesy($output) / 2));

        // Determine the individual Red, Green, and Blue values.
        $red = ($rgb >> 16) & 0xFF;
        $green = ($rgb >> 8) & 0xFF;
        $blue = $rgb & 0xFF;

        // In grayscale, these 3 values should be the same.
        $this->assertEquals($red === $green && $green === $blue && $blue === $red, true);

        imagedestroy($output);

        // Clean-up after
        self::removeFileIfExists([$originalImageSource, $bnwImageSource]);
    }
}
