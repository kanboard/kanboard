<?php

/**
 * This file is part of PHPWord - A pure PHP library for reading and writing
 * word processing documents.
 *
 * PHPWord is free software distributed under the terms of the GNU Lesser
 * General Public License version 3 as published by the Free Software Foundation.
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code. For the full list of
 * contributors, visit https://github.com/PHPOffice/PHPWord/contributors.
 *
 * @see         https://github.com/PHPOffice/PHPWord
 *
 * @license     http://www.gnu.org/licenses/lgpl.txt LGPL version 3
 */

namespace PhpOffice\PhpWord\Element;

use PhpOffice\PhpWord\Exception\CreateTemporaryFileException;
use PhpOffice\PhpWord\Exception\InvalidImageException;
use PhpOffice\PhpWord\Exception\UnsupportedImageTypeException;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\Shared\ZipArchive;
use PhpOffice\PhpWord\Style\Image as ImageStyle;

/**
 * Image element.
 */
class Image extends AbstractElement
{
    /**
     * Image source type constants.
     */
    const SOURCE_LOCAL = 'local'; // Local images
    const SOURCE_GD = 'gd'; // Generated using GD
    const SOURCE_ARCHIVE = 'archive'; // Image in archives zip://$archive#$image
    const SOURCE_STRING = 'string'; // Image from string

    /**
     * Image source.
     *
     * @var string
     */
    private $source;

    /**
     * Source type: local|gd|archive.
     *
     * @var string
     */
    private $sourceType;

    /**
     * Image style.
     *
     * @var ?ImageStyle
     */
    private $style;

    /**
     * Is watermark.
     *
     * @var bool
     */
    private $watermark;

    /**
     * Name of image.
     *
     * @var string
     */
    private $name;

    /**
     * Image type.
     *
     * @var string
     */
    private $imageType;

    /**
     * Image create function.
     *
     * @var string
     */
    private $imageCreateFunc;

    /**
     * Image function.
     *
     * @var null|callable(resource): void
     */
    private $imageFunc;

    /**
     * Image extension.
     *
     * @var string
     */
    private $imageExtension;

    /**
     * Image quality.
     *
     * Functions imagepng() and imagejpeg() have an optional parameter for
     * quality.
     *
     * @var null|int
     */
    private $imageQuality;

    /**
     * Is memory image.
     *
     * @var bool
     */
    private $memoryImage;

    /**
     * Image target file name.
     *
     * @var string
     */
    private $target;

    /**
     * Image media index.
     *
     * @var int
     */
    private $mediaIndex;

    /**
     * Has media relation flag; true for Link, Image, and Object.
     *
     * @var bool
     */
    protected $mediaRelation = true;

    /**
     * Create new image element.
     *
     * @param string $source
     * @param mixed $style
     * @param bool $watermark
     * @param string $name
     */
    public function __construct($source, $style = null, $watermark = false, $name = null)
    {
        $this->source = $source;
        $this->style = $this->setNewStyle(new ImageStyle(), $style, true);
        $this->setIsWatermark($watermark);
        $this->setName($name);

        $this->checkImage();
    }

    /**
     * Get Image style.
     *
     * @return ?ImageStyle
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * Get image source.
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Get image source type.
     *
     * @return string
     */
    public function getSourceType()
    {
        return $this->sourceType;
    }

    /**
     * Sets the image name.
     *
     * @param string $value
     */
    public function setName($value): void
    {
        $this->name = $value;
    }

    /**
     * Get image name.
     *
     * @return null|string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get image media ID.
     *
     * @return string
     */
    public function getMediaId()
    {
        return md5($this->source);
    }

    /**
     * Get is watermark.
     *
     * @return bool
     */
    public function isWatermark()
    {
        return $this->watermark;
    }

    /**
     * Set is watermark.
     *
     * @param bool $value
     */
    public function setIsWatermark($value): void
    {
        $this->watermark = $value;
    }

    /**
     * Get image type.
     *
     * @return string
     */
    public function getImageType()
    {
        return $this->imageType;
    }

    /**
     * Get image create function.
     *
     * @return string
     */
    public function getImageCreateFunction()
    {
        return $this->imageCreateFunc;
    }

    /**
     * Get image function.
     *
     * @return null|callable(resource): void
     */
    public function getImageFunction(): ?callable
    {
        return $this->imageFunc;
    }

    /**
     * Get image quality.
     */
    public function getImageQuality(): ?int
    {
        return $this->imageQuality;
    }

    /**
     * Get image extension.
     *
     * @return string
     */
    public function getImageExtension()
    {
        return $this->imageExtension;
    }

    /**
     * Get is memory image.
     *
     * @return bool
     */
    public function isMemImage()
    {
        return $this->memoryImage;
    }

    /**
     * Get target file name.
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set target file name.
     *
     * @param string $value
     */
    public function setTarget($value): void
    {
        $this->target = $value;
    }

    /**
     * Get media index.
     *
     * @return int
     */
    public function getMediaIndex()
    {
        return $this->mediaIndex;
    }

    /**
     * Set media index.
     *
     * @param int $value
     */
    public function setMediaIndex($value): void
    {
        $this->mediaIndex = $value;
    }

    /**
     * Get image string.
     */
    public function getImageString(): ?string
    {
        $source = $this->source;
        $actualSource = null;
        $imageBinary = null;
        $isTemp = false;

        // Get actual source from archive image or other source
        // Return null if not found
        if ($this->sourceType == self::SOURCE_ARCHIVE) {
            $source = substr($source, 6);
            [$zipFilename, $imageFilename] = explode('#', $source);

            $zip = new ZipArchive();
            if ($zip->open($zipFilename) !== false) {
                if ($zip->locateName($imageFilename) !== false) {
                    $isTemp = true;
                    $zip->extractTo(Settings::getTempDir(), $imageFilename);
                    $actualSource = Settings::getTempDir() . DIRECTORY_SEPARATOR . $imageFilename;
                }
            }
            $zip->close();
        } else {
            $actualSource = $source;
        }

        // Can't find any case where $actualSource = null hasn't captured by
        // preceding exceptions. Please uncomment when you find the case and
        // put the case into Element\ImageTest.
        // if ($actualSource === null) {
        //     return null;
        // }

        // Read image binary data and convert to hex/base64 string
        if ($this->sourceType == self::SOURCE_GD) {
            $imageResource = call_user_func($this->imageCreateFunc, $actualSource);
            if ($this->imageType === 'image/png') {
                // PNG images need to preserve alpha channel information
                imagesavealpha($imageResource, true);
            }
            ob_start();
            $callback = $this->imageFunc;
            $callback($imageResource);
            $imageBinary = ob_get_contents();
            ob_end_clean();
        } elseif ($this->sourceType == self::SOURCE_STRING) {
            $imageBinary = $this->source;
        } else {
            $fileHandle = fopen($actualSource, 'rb', false);
            $fileSize = filesize($actualSource);
            if ($fileHandle !== false && $fileSize > 0) {
                $imageBinary = fread($fileHandle, $fileSize);
                fclose($fileHandle);
            }
        }

        // Delete temporary file if necessary
        if ($isTemp === true) {
            @unlink($actualSource);
        }

        return $imageBinary;
    }

    /**
     * Get image string data.
     *
     * @param bool $base64
     *
     * @return null|string
     *
     * @since 0.11.0
     */
    public function getImageStringData($base64 = false)
    {
        $imageBinary = $this->getImageString();
        if ($imageBinary === null) {
            return null;
        }

        if ($base64) {
            return base64_encode($imageBinary);
        }

        return bin2hex($imageBinary);
    }

    /**
     * Check memory image, supported type, image functions, and proportional width/height.
     */
    private function checkImage(): void
    {
        $this->setSourceType();

        // Check image data
        if ($this->sourceType == self::SOURCE_ARCHIVE) {
            $imageData = $this->getArchiveImageSize($this->source);
        } elseif ($this->sourceType == self::SOURCE_STRING) {
            $imageData = @getimagesizefromstring($this->source);
        } else {
            $imageData = @getimagesize($this->source);
        }
        if (!is_array($imageData)) {
            throw new InvalidImageException(sprintf('Invalid image: %s', $this->source));
        }
        [$actualWidth, $actualHeight, $imageType] = $imageData;

        // Check image type support
        $supportedTypes = [IMAGETYPE_JPEG, IMAGETYPE_GIF, IMAGETYPE_PNG];
        if ($this->sourceType != self::SOURCE_GD && $this->sourceType != self::SOURCE_STRING) {
            $supportedTypes = array_merge($supportedTypes, [IMAGETYPE_BMP, IMAGETYPE_TIFF_II, IMAGETYPE_TIFF_MM]);
        }
        if (!in_array($imageType, $supportedTypes)) {
            throw new UnsupportedImageTypeException();
        }

        // Define image functions
        $this->imageType = image_type_to_mime_type($imageType);
        $this->setFunctions();
        $this->setProportionalSize($actualWidth, $actualHeight);
    }

    /**
     * Set source type.
     */
    private function setSourceType(): void
    {
        if (stripos(strrev($this->source), strrev('.php')) === 0) {
            $this->memoryImage = true;
            $this->sourceType = self::SOURCE_GD;
        } elseif (strpos($this->source, 'zip://') !== false) {
            $this->memoryImage = false;
            $this->sourceType = self::SOURCE_ARCHIVE;
        } elseif (filter_var($this->source, FILTER_VALIDATE_URL) !== false) {
            $this->memoryImage = true;
            if (strpos($this->source, 'https') === 0) {
                $fileContent = file_get_contents($this->source);
                $this->source = $fileContent;
                $this->sourceType = self::SOURCE_STRING;
            } else {
                $this->sourceType = self::SOURCE_GD;
            }
        } elseif ((strpos($this->source, chr(0)) === false) && @file_exists($this->source)) {
            $this->memoryImage = false;
            $this->sourceType = self::SOURCE_LOCAL;
        } else {
            $this->memoryImage = true;
            $this->sourceType = self::SOURCE_STRING;
        }
    }

    /**
     * Get image size from archive.
     *
     * @since 0.12.0 Throws CreateTemporaryFileException.
     *
     * @param string $source
     *
     * @return null|array
     */
    private function getArchiveImageSize($source)
    {
        $imageData = null;
        $source = substr($source, 6);
        [$zipFilename, $imageFilename] = explode('#', $source);

        $tempFilename = tempnam(Settings::getTempDir(), 'PHPWordImage');
        if (false === $tempFilename) {
            throw new CreateTemporaryFileException(); // @codeCoverageIgnore
        }

        $zip = new ZipArchive();
        if ($zip->open($zipFilename) !== false) {
            if ($zip->locateName($imageFilename) !== false) {
                $imageContent = $zip->getFromName($imageFilename);
                if ($imageContent !== false) {
                    file_put_contents($tempFilename, $imageContent);
                    $imageData = getimagesize($tempFilename);
                    unlink($tempFilename);
                }
            }
            $zip->close();
        }

        return $imageData;
    }

    /**
     * Set image functions and extensions.
     */
    private function setFunctions(): void
    {
        switch ($this->imageType) {
            case 'image/png':
                $this->imageCreateFunc = $this->sourceType == self::SOURCE_STRING ? 'imagecreatefromstring' : 'imagecreatefrompng';
                $this->imageFunc = function ($resource): void {
                    imagepng($resource, null, $this->imageQuality);
                };
                $this->imageExtension = 'png';
                $this->imageQuality = -1;

                break;
            case 'image/gif':
                $this->imageCreateFunc = $this->sourceType == self::SOURCE_STRING ? 'imagecreatefromstring' : 'imagecreatefromgif';
                $this->imageFunc = function ($resource): void {
                    imagegif($resource);
                };
                $this->imageExtension = 'gif';
                $this->imageQuality = null;

                break;
            case 'image/jpeg':
            case 'image/jpg':
                $this->imageCreateFunc = $this->sourceType == self::SOURCE_STRING ? 'imagecreatefromstring' : 'imagecreatefromjpeg';
                $this->imageFunc = function ($resource): void {
                    imagejpeg($resource, null, $this->imageQuality);
                };
                $this->imageExtension = 'jpg';
                $this->imageQuality = 100;

                break;
            case 'image/bmp':
            case 'image/x-ms-bmp':
                $this->imageType = 'image/bmp';
                $this->imageFunc = null;
                $this->imageExtension = 'bmp';
                $this->imageQuality = null;

                break;
            case 'image/tiff':
                $this->imageType = 'image/tiff';
                $this->imageFunc = null;
                $this->imageExtension = 'tif';
                $this->imageQuality = null;

                break;
        }
    }

    /**
     * Set proportional width/height if one dimension not available.
     *
     * @param int $actualWidth
     * @param int $actualHeight
     */
    private function setProportionalSize($actualWidth, $actualHeight): void
    {
        $styleWidth = $this->style->getWidth();
        $styleHeight = $this->style->getHeight();
        if (!($styleWidth && $styleHeight)) {
            if ($styleWidth == null && $styleHeight == null) {
                $this->style->setWidth($actualWidth);
                $this->style->setHeight($actualHeight);
            } elseif ($styleWidth) {
                $this->style->setHeight($actualHeight * ($styleWidth / $actualWidth));
            } else {
                $this->style->setWidth($actualWidth * ($styleHeight / $actualHeight));
            }
        }
    }
}
