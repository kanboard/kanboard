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

use PhpOffice\PhpWord\Exception\InvalidObjectException;
use PhpOffice\PhpWord\Style\Image as ImageStyle;

/**
 * OLEObject element.
 */
class OLEObject extends AbstractElement
{
    /**
     * Ole-Object Src.
     *
     * @var string
     */
    private $source;

    /**
     * Image Style.
     *
     * @var ?ImageStyle
     */
    private $style;

    /**
     * Icon.
     *
     * @var string
     */
    private $icon;

    /**
     * Image Relation ID.
     *
     * @var int
     */
    private $imageRelationId;

    /**
     * Has media relation flag; true for Link, Image, and Object.
     *
     * @var bool
     */
    protected $mediaRelation = true;

    /**
     * Create a new Ole-Object Element.
     *
     * @param string $source
     * @param mixed $style
     */
    public function __construct($source, $style = null)
    {
        $supportedTypes = ['xls', 'doc', 'ppt', 'xlsx', 'docx', 'pptx'];
        $pathInfo = pathinfo($source);

        if (file_exists($source) && in_array($pathInfo['extension'], $supportedTypes)) {
            $ext = $pathInfo['extension'];
            if (strlen($ext) == 4 && strtolower(substr($ext, -1)) == 'x') {
                $ext = substr($ext, 0, -1);
            }

            $this->source = $source;
            $this->style = $this->setNewStyle(new ImageStyle(), $style, true);
            $this->icon = realpath(__DIR__ . "/../resources/{$ext}.png");

            return;
        }

        throw new InvalidObjectException();
    }

    /**
     * Get object source.
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Get object style.
     *
     * @return ?ImageStyle
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * Get object icon.
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Get image relation ID.
     *
     * @return int
     */
    public function getImageRelationId()
    {
        return $this->imageRelationId;
    }

    /**
     * Set Image Relation ID.
     *
     * @param int $rId
     */
    public function setImageRelationId($rId): void
    {
        $this->imageRelationId = $rId;
    }
}
