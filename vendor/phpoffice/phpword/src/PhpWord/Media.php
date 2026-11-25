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

namespace PhpOffice\PhpWord;

use PhpOffice\PhpWord\Element\Image;
use PhpOffice\PhpWord\Exception\Exception;

/**
 * Media collection.
 */
class Media
{
    /**
     * Media elements.
     *
     * @var array
     */
    private static $elements = [];

    /**
     * Add new media element.
     *
     * @since 0.10.0
     * @since 0.9.2
     *
     * @param string $container section|headerx|footerx|footnote|endnote
     * @param string $mediaType image|object|link
     * @param string $source
     *
     * @return int
     */
    public static function addElement($container, $mediaType, $source, ?Image $image = null)
    {
        // Assign unique media Id and initiate media container if none exists
        $mediaId = md5($container . $source);
        if (!isset(self::$elements[$container])) {
            self::$elements[$container] = [];
        }

        // Add media if not exists or point to existing media
        if (!isset(self::$elements[$container][$mediaId])) {
            $mediaCount = self::countElements($container);
            $mediaTypeCount = self::countElements($container, $mediaType);
            ++$mediaTypeCount;
            $rId = ++$mediaCount;
            $target = null;
            $mediaData = ['mediaIndex' => $mediaTypeCount];

            switch ($mediaType) {
                // Images
                case 'image':
                    if (null === $image) {
                        throw new Exception('Image object not assigned.');
                    }
                    $isMemImage = $image->isMemImage();
                    $extension = $image->getImageExtension();
                    $mediaData['imageExtension'] = $extension;
                    $mediaData['imageType'] = $image->getImageType();
                    if ($isMemImage) {
                        $mediaData['isMemImage'] = true;
                        $mediaData['imageString'] = $image->getImageString();
                    }
                    $target = "{$container}_image{$mediaTypeCount}.{$extension}";
                    $image->setTarget($target);
                    $image->setMediaIndex($mediaTypeCount);

                    break;
                    // Objects
                case 'object':
                    $target = "{$container}_oleObject{$mediaTypeCount}.bin";

                    break;
                    // Links
                case 'link':
                    $target = $source;

                    break;
            }

            $mediaData['source'] = $source;
            $mediaData['target'] = $target;
            $mediaData['type'] = $mediaType;
            $mediaData['rID'] = $rId;
            self::$elements[$container][$mediaId] = $mediaData;

            return $rId;
        }

        $mediaData = self::$elements[$container][$mediaId];
        if (null !== $image) {
            $image->setTarget($mediaData['target']);
            $image->setMediaIndex($mediaData['mediaIndex']);
        }

        return $mediaData['rID'];
    }

    /**
     * Get media elements count.
     *
     * @param string $container section|headerx|footerx|footnote|endnote
     * @param string $mediaType image|object|link
     *
     * @return int
     *
     * @since 0.10.0
     */
    public static function countElements($container, $mediaType = null)
    {
        $mediaCount = 0;

        if (isset(self::$elements[$container])) {
            foreach (self::$elements[$container] as $mediaData) {
                if (null !== $mediaType) {
                    if ($mediaType == $mediaData['type']) {
                        ++$mediaCount;
                    }
                } else {
                    ++$mediaCount;
                }
            }
        }

        return $mediaCount;
    }

    /**
     * Get media elements.
     *
     * @param string $container section|headerx|footerx|footnote|endnote
     * @param string $type image|object|link
     *
     * @return array
     *
     * @since 0.10.0
     */
    public static function getElements($container, $type = null)
    {
        $elements = [];

        // If header/footer, search for headerx and footerx where x is number
        if ($container == 'header' || $container == 'footer') {
            foreach (self::$elements as $key => $val) {
                if (substr($key, 0, 6) == $container) {
                    $elements[$key] = $val;
                }
            }

            return $elements;
        }

        if (!isset(self::$elements[$container])) {
            return $elements;
        }

        return self::getElementsByType($container, $type);
    }

    /**
     * Get elements by media type.
     *
     * @param string $container section|footnote|endnote
     * @param string $type image|object|link
     *
     * @return array
     *
     * @since 0.11.0 Splitted from `getElements` to reduce complexity
     */
    private static function getElementsByType($container, $type = null)
    {
        $elements = [];

        foreach (self::$elements[$container] as $key => $data) {
            if ($type !== null) {
                if ($type == $data['type']) {
                    $elements[$key] = $data;
                }
            } else {
                $elements[$key] = $data;
            }
        }

        return $elements;
    }

    /**
     * Reset media elements.
     */
    public static function resetElements(): void
    {
        self::$elements = [];
    }
}
