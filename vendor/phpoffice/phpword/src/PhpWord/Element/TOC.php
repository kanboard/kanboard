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

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\Style\TOC as TOCStyle;

/**
 * Table of contents.
 */
class TOC extends AbstractElement
{
    /**
     * TOC style.
     *
     * @var TOCStyle
     */
    private $tocStyle;

    /**
     * Font style.
     *
     * @var Font|string
     */
    private $fontStyle;

    /**
     * Min title depth to show.
     *
     * @var int
     */
    private $minDepth = 1;

    /**
     * Max title depth to show.
     *
     * @var int
     */
    private $maxDepth = 9;

    /**
     * Create a new Table-of-Contents Element.
     *
     * @param mixed $fontStyle
     * @param array $tocStyle
     * @param int $minDepth
     * @param int $maxDepth
     */
    public function __construct($fontStyle = null, $tocStyle = null, $minDepth = 1, $maxDepth = 9)
    {
        $this->tocStyle = new TOCStyle();

        if (null !== $tocStyle && is_array($tocStyle)) {
            $this->tocStyle->setStyleByArray($tocStyle);
        }

        if (null !== $fontStyle && is_array($fontStyle)) {
            $this->fontStyle = new Font();
            $this->fontStyle->setStyleByArray($fontStyle);
        } else {
            $this->fontStyle = $fontStyle;
        }

        $this->minDepth = $minDepth;
        $this->maxDepth = $maxDepth;
    }

    /**
     * Get all titles.
     *
     * @return array
     */
    public function getTitles()
    {
        if (!$this->phpWord instanceof PhpWord) {
            return [];
        }

        $titles = $this->phpWord->getTitles()->getItems();
        foreach ($titles as $i => $title) {
            /** @var Title $title Type hint */
            $depth = $title->getDepth();
            if ($this->minDepth > $depth) {
                unset($titles[$i]);
            }
            if (($this->maxDepth != 0) && ($this->maxDepth < $depth)) {
                unset($titles[$i]);
            }
        }

        return $titles;
    }

    /**
     * Get TOC Style.
     *
     * @return TOCStyle
     */
    public function getStyleTOC()
    {
        return $this->tocStyle;
    }

    /**
     * Get Font Style.
     *
     * @return Font|string
     */
    public function getStyleFont()
    {
        return $this->fontStyle;
    }

    /**
     * Set max depth.
     *
     * @param int $value
     */
    public function setMaxDepth($value): void
    {
        $this->maxDepth = $value;
    }

    /**
     * Get Max Depth.
     *
     * @return int Max depth of titles
     */
    public function getMaxDepth()
    {
        return $this->maxDepth;
    }

    /**
     * Set min depth.
     *
     * @param int $value
     */
    public function setMinDepth($value): void
    {
        $this->minDepth = $value;
    }

    /**
     * Get Min Depth.
     *
     * @return int Min depth of titles
     */
    public function getMinDepth()
    {
        return $this->minDepth;
    }
}
