<?php

/**
 * This file is part of PHPWord - A pure PHP library for reading and writing
 * word processing documents.
 * PHPWord is free software distributed under the terms of the GNU Lesser
 * General Public License version 3 as published by the Free Software Foundation.
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code. For the full list of
 * contributors, visit https://github.com/PHPOffice/PHPWord/contributors.
 *
 * @see         https://github.com/PHPOffice/PHPWord
 *
 * @license     http://www.gnu.org/licenses/lgpl.txt LGPL version 3
 */

namespace PhpOffice\PhpWord\Style;

/**
 * TextBox style.
 *
 * @since 0.11.0
 */
class TextBox extends Image
{
    /**
     * margin top.
     *
     * @var null|int
     */
    private $innerMarginTop;

    /**
     * margin left.
     *
     * @var null|int
     */
    private $innerMarginLeft;

    /**
     * margin right.
     *
     * @var null|int
     */
    private $innerMarginRight;

    /**
     * Cell margin bottom.
     *
     * @var null|int
     */
    private $innerMarginBottom;

    /**
     * border size.
     *
     * @var null|int
     */
    private $borderSize;

    /**
     * border color.
     *
     * @var null|string
     */
    private $borderColor;

    /**
     * background color.
     *
     * @var null|string
     */
    private $bgColor;

    /**
     * Set background color.
     */
    public function setBgColor(?string $value = null): void
    {
        $this->bgColor = $value;
    }

    /**
     * Get background color.
     */
    public function getBgColor(): ?string
    {
        return $this->bgColor;
    }

    /**
     * Set margin top.
     */
    public function setInnerMarginTop(?int $value = null): void
    {
        $this->innerMarginTop = $value;
    }

    /**
     * Get margin top.
     */
    public function getInnerMarginTop(): ?int
    {
        return $this->innerMarginTop;
    }

    /**
     * Set margin left.
     */
    public function setInnerMarginLeft(?int $value = null): void
    {
        $this->innerMarginLeft = $value;
    }

    /**
     * Get margin left.
     */
    public function getInnerMarginLeft(): ?int
    {
        return $this->innerMarginLeft;
    }

    /**
     * Set margin right.
     */
    public function setInnerMarginRight(?int $value = null): void
    {
        $this->innerMarginRight = $value;
    }

    /**
     * Get margin right.
     */
    public function getInnerMarginRight(): ?int
    {
        return $this->innerMarginRight;
    }

    /**
     * Set margin bottom.
     */
    public function setInnerMarginBottom(?int $value = null): void
    {
        $this->innerMarginBottom = $value;
    }

    /**
     * Get margin bottom.
     */
    public function getInnerMarginBottom(): ?int
    {
        return $this->innerMarginBottom;
    }

    /**
     * Set TLRB cell margin.
     *
     * @param null|int $value Margin in twips
     */
    public function setInnerMargin(?int $value = null): void
    {
        $this->setInnerMarginTop($value);
        $this->setInnerMarginLeft($value);
        $this->setInnerMarginRight($value);
        $this->setInnerMarginBottom($value);
    }

    /**
     * Get cell margin.
     *
     * @return int[]
     */
    public function getInnerMargin(): array
    {
        return [$this->innerMarginLeft, $this->innerMarginTop, $this->innerMarginRight, $this->innerMarginBottom];
    }

    /**
     * Has inner margin?
     */
    public function hasInnerMargins(): bool
    {
        $hasInnerMargins = false;
        $margins = $this->getInnerMargin();
        $numMargins = count($margins);
        for ($i = 0; $i < $numMargins; ++$i) {
            if ($margins[$i] !== null) {
                $hasInnerMargins = true;
            }
        }

        return $hasInnerMargins;
    }

    /**
     * Set border size.
     *
     * @param null|int $value Size in points
     */
    public function setBorderSize(?int $value = null): void
    {
        $this->borderSize = $value;
    }

    /**
     * Get border size.
     */
    public function getBorderSize(): ?int
    {
        return $this->borderSize;
    }

    /**
     * Set border color.
     */
    public function setBorderColor(?string $value = null): void
    {
        $this->borderColor = $value;
    }

    /**
     * Get border color.
     */
    public function getBorderColor(): ?string
    {
        return $this->borderColor;
    }
}
