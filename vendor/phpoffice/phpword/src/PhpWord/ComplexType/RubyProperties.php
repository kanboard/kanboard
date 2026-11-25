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

namespace PhpOffice\PhpWord\ComplexType;

use InvalidArgumentException;

/**
 * Ruby properties.
 *
 * @see https://learn.microsoft.com/en-us/dotnet/api/documentformat.openxml.wordprocessing.rubyproperties?view=openxml-3.0.1
 */
class RubyProperties
{
    const ALIGNMENT_CENTER = 'center';
    const ALIGNMENT_DISTRIBUTE_LETTER = 'distributeLetter';
    const ALIGNMENT_DISTRIBUTE_SPACE = 'distributeSpace';
    const ALIGNMENT_LEFT = 'left';
    const ALIGNMENT_RIGHT = 'right';
    const ALIGNMENT_RIGHT_VERTICAL = 'rightVertical';

    /**
     * Ruby alignment (w:rubyAlign).
     *
     * @var string
     */
    private $alignment;

    /**
     * Ruby font face size (w:hps).
     *
     * @var float
     */
    private $fontFaceSize;

    /**
     * Ruby font points above base text (w:hpsRaise).
     *
     * @var float
     */
    private $fontPointsAboveText;

    /**
     * Ruby font size for base text (w:hpsBaseText).
     *
     * @var float
     */
    private $baseTextFontSize;

    /**
     * Ruby type/language id (w:lid).
     *
     * @var string
     */
    private $languageId;

    /**
     * Create a new RubyProperties object.
     */
    public function __construct()
    {
        // these defaults came from opening a new Word doc, adding some ruby text to some
        // Japanese text, and copying out the defaults.
        $this->alignment = self::ALIGNMENT_DISTRIBUTE_SPACE;
        $this->fontFaceSize = 12;
        $this->fontPointsAboveText = 22;
        $this->languageId = 'ja-JP';
        $this->baseTextFontSize = 24;
    }

    /**
     * Get the ruby alignment.
     */
    public function getAlignment(): string
    {
        return $this->alignment;
    }

    /**
     * Set the Ruby Alignment (center, distributeLetter, distributeSpace, left, right, rightVertical).
     */
    public function setAlignment(string $alignment): self
    {
        $alignmentTypes = [
            self::ALIGNMENT_CENTER,
            self::ALIGNMENT_DISTRIBUTE_LETTER,
            self::ALIGNMENT_DISTRIBUTE_SPACE,
            self::ALIGNMENT_LEFT,
            self::ALIGNMENT_RIGHT,
            self::ALIGNMENT_RIGHT_VERTICAL,
        ];

        if (in_array($alignment, $alignmentTypes)) {
            $this->alignment = $alignment;
        } else {
            throw new InvalidArgumentException('Invalid value, alignments of ' . implode(', ', $alignmentTypes) . ' possible');
        }

        return $this;
    }

    /**
     * Get the ruby font face size.
     */
    public function getFontFaceSize(): float
    {
        return $this->fontFaceSize;
    }

    /**
     * Set the ruby font face size.
     */
    public function setFontFaceSize(float $size): self
    {
        $this->fontFaceSize = $size;

        return $this;
    }

    /**
     * Get the ruby font points above base text.
     */
    public function getFontPointsAboveBaseText(): float
    {
        return $this->fontPointsAboveText;
    }

    /**
     * Set the ruby font points above base text.
     */
    public function setFontPointsAboveBaseText(float $size): self
    {
        $this->fontPointsAboveText = $size;

        return $this;
    }

    /**
     * Get the ruby font size for base text.
     */
    public function getFontSizeForBaseText(): float
    {
        return $this->baseTextFontSize;
    }

    /**
     * Set the ruby font size for base text.
     */
    public function setFontSizeForBaseText(float $size): self
    {
        $this->baseTextFontSize = $size;

        return $this;
    }

    /**
     * Get the ruby language id.
     */
    public function getLanguageId(): string
    {
        return $this->languageId;
    }

    /**
     * Set the ruby language id.
     */
    public function setLanguageId(string $langId): self
    {
        $this->languageId = $langId;

        return $this;
    }
}
