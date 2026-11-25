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

namespace PhpOffice\PhpWord\Style;

use PhpOffice\PhpWord\Exception\InvalidStyleException;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\Shared\Text;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\TextAlignment;

/**
 * Paragraph style.
 *
 * OOXML:
 * - General: alignment, outline level
 * - Indentation: left, right, firstline, hanging
 * - Spacing: before, after, line spacing
 * - Pagination: widow control, keep next, keep line, page break before
 * - Formatting exception: suppress line numbers, don't hyphenate
 * - Textbox options
 * - Tabs
 * - Shading
 * - Borders
 *
 * OpenOffice:
 * - Indents & spacing
 * - Alignment
 * - Text flow
 * - Outline & numbering
 * - Tabs
 * - Dropcaps
 * - Tabs
 * - Borders
 * - Background
 *
 * @see  http://www.schemacentral.com/sc/ooxml/t-w_CT_PPr.html
 */
class Paragraph extends Border
{
    /**
     * @const int One line height equals 240 twip
     */
    const LINE_HEIGHT = 240;

    /**
     * Aliases.
     *
     * @var array
     */
    protected $aliases = ['line-height' => 'lineHeight', 'line-spacing' => 'spacing'];

    /**
     * Parent style.
     *
     * @var string
     */
    private $basedOn = 'Normal';

    /**
     * Style for next paragraph.
     *
     * @var string
     */
    private $next;

    /**
     * @var string
     */
    private $alignment = '';

    /**
     * Indentation.
     *
     * @var null|Indentation
     */
    private $indentation;

    /**
     * Spacing.
     *
     * @var Spacing
     */
    private $spacing;

    /**
     * Text line height.
     *
     * @var null|float|int
     */
    private $lineHeight;

    /**
     * Allow first/last line to display on a separate page.
     *
     * @var bool
     */
    private $widowControl = true;

    /**
     * Keep paragraph with next paragraph.
     *
     * @var bool
     */
    private $keepNext = false;

    /**
     * Keep all lines on one page.
     *
     * @var bool
     */
    private $keepLines = false;

    /**
     * Start paragraph on next page.
     *
     * @var bool
     */
    private $pageBreakBefore = false;

    /**
     * Numbering style name.
     *
     * @var string
     */
    private $numStyle;

    /**
     * Numbering level.
     *
     * @var int
     */
    private $numLevel = 0;

    /**
     * Set of Custom Tab Stops.
     *
     * @var Tab[]
     */
    private $tabs = [];

    /**
     * Shading.
     *
     * @var Shading
     */
    private $shading;

    /**
     * Ignore Spacing Above and Below When Using Identical Styles.
     *
     * @var bool
     */
    private $contextualSpacing = false;

    /**
     * Right to Left Paragraph Layout.
     *
     * @var ?bool
     */
    private $bidi;

    /**
     * Vertical Character Alignment on Line.
     *
     * @var string
     */
    private $textAlignment;

    /**
     * Suppress hyphenation for paragraph.
     *
     * @var bool
     */
    private $suppressAutoHyphens = false;

    /**
     * Set Style value.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return self
     */
    public function setStyleValue($key, $value)
    {
        $key = Text::removeUnderscorePrefix($key);
        if ('indent' == $key || 'hanging' == $key) {
            $value = $value * 720;  // 720 twips is 0.5 inch
        }

        return parent::setStyleValue($key, $value);
    }

    /**
     * Get style values.
     *
     * An experiment to retrieve all style values in one function. This will
     * reduce function call and increase cohesion between functions. Should be
     * implemented in all styles.
     *
     * @ignoreScrutinizerPatch
     *
     * @return array
     */
    public function getStyleValues()
    {
        $styles = [
            'name' => $this->getStyleName(),
            'basedOn' => $this->getBasedOn(),
            'next' => $this->getNext(),
            'alignment' => $this->getAlignment(),
            'indentation' => $this->getIndentation(),
            'spacing' => $this->getSpace(),
            'pagination' => [
                'widowControl' => $this->hasWidowControl(),
                'keepNext' => $this->isKeepNext(),
                'keepLines' => $this->isKeepLines(),
                'pageBreak' => $this->hasPageBreakBefore(),
            ],
            'numbering' => [
                'style' => $this->getNumStyle(),
                'level' => $this->getNumLevel(),
            ],
            'tabs' => $this->getTabs(),
            'shading' => $this->getShading(),
            'contextualSpacing' => $this->hasContextualSpacing(),
            'bidi' => $this->isBidi(),
            'textAlignment' => $this->getTextAlignment(),
            'suppressAutoHyphens' => $this->hasSuppressAutoHyphens(),
        ];

        return $styles;
    }

    /**
     * @since 0.13.0
     *
     * @return string
     */
    public function getAlignment()
    {
        return $this->alignment;
    }

    /**
     * @since 0.13.0
     *
     * @param string $value
     *
     * @return self
     */
    public function setAlignment($value)
    {
        if (Jc::isValid($value)) {
            $this->alignment = $value;
        }

        return $this;
    }

    /**
     * Get parent style ID.
     *
     * @return string
     */
    public function getBasedOn()
    {
        return $this->basedOn;
    }

    /**
     * Set parent style ID.
     *
     * @param string $value
     *
     * @return self
     */
    public function setBasedOn($value = 'Normal')
    {
        $this->basedOn = $value;

        return $this;
    }

    /**
     * Get style for next paragraph.
     *
     * @return string
     */
    public function getNext()
    {
        return $this->next;
    }

    /**
     * Set style for next paragraph.
     *
     * @param string $value
     *
     * @return self
     */
    public function setNext($value = null)
    {
        $this->next = $value;

        return $this;
    }

    /**
     * Get hanging.
     */
    public function getHanging(): ?float
    {
        return $this->getChildStyleValue($this->indentation, 'hanging');
    }

    /**
     * Get indentation.
     *
     * @deprecated 1.4.0 Use getIndentLeft
     */
    public function getIndent(): ?float
    {
        return $this->getChildStyleValue($this->indentation, 'left');
    }

    /**
     * Get indentation.
     */
    public function getIndentation(): ?Indentation
    {
        return $this->indentation;
    }

    /**
     * Get firstLine.
     */
    public function getIndentFirstLine(): ?float
    {
        return $this->getChildStyleValue($this->indentation, 'firstLine');
    }

    /**
     * Get left indentation.
     */
    public function getIndentLeft(): ?float
    {
        return $this->getChildStyleValue($this->indentation, 'left');
    }

    /**
     * Get right indentation.
     */
    public function getIndentRight(): ?float
    {
        return $this->getChildStyleValue($this->indentation, 'right');
    }

    /**
     * Set hanging.
     *
     * @deprecated 1.4.0 Use setIndentHanging
     */
    public function setHanging(?float $value = null): self
    {
        return $this->setIndentation(['hanging' => $value]);
    }

    /**
     * Set indentation.
     *
     * @deprecated 1.4.0 Use setIndentLeft
     */
    public function setIndent(?float $value = null): self
    {
        return $this->setIndentation(['left' => $value]);
    }

    /**
     * Set indentation.
     *
     * @param array{
     *     left?:null|float|int|numeric-string,
     *     right?:null|float|int|numeric-string,
     *     hanging?:null|float|int|numeric-string,
     *     firstLine?:null|float|int|numeric-string
     * } $value
     */
    public function setIndentation(array $value = []): self
    {
        $value = array_map(function ($indent) {
            if (is_string($indent) || is_numeric($indent)) {
                $indent = $this->setFloatVal($indent);
            }

            return $indent;
        }, $value);
        $this->setObjectVal($value, 'Indentation', $this->indentation);

        return $this;
    }

    /**
     * Set hanging indentation.
     */
    public function setIndentHanging(?float $value = null): self
    {
        return $this->setIndentation(['hanging' => $value]);
    }

    /**
     * Set firstline indentation.
     */
    public function setIndentFirstLine(?float $value = null): self
    {
        return $this->setIndentation(['firstLine' => $value]);
    }

    /**
     * Set firstlineChars indentation.
     */
    public function setIndentFirstLineChars(int $value = 0): self
    {
        return $this->setIndentation(['firstLineChars' => $value]);
    }

    /**
     * Set left indentation.
     */
    public function setIndentLeft(?float $value = null): self
    {
        return $this->setIndentation(['left' => $value]);
    }

    /**
     * Set right indentation.
     */
    public function setIndentRight(?float $value = null): self
    {
        return $this->setIndentation(['right' => $value]);
    }

    /**
     * Get spacing.
     *
     * @return Spacing
     *
     * @todo Rename to getSpacing in 1.0
     */
    public function getSpace()
    {
        return $this->spacing;
    }

    /**
     * Set spacing.
     *
     * @param mixed $value
     *
     * @return self
     *
     * @todo Rename to setSpacing in 1.0
     */
    public function setSpace($value = null)
    {
        $this->setObjectVal($value, 'Spacing', $this->spacing);

        return $this;
    }

    /**
     * Get space before paragraph.
     *
     * @return null|float|int
     */
    public function getSpaceBefore()
    {
        return $this->getChildStyleValue($this->spacing, 'before');
    }

    /**
     * Set space before paragraph.
     *
     * @param null|float|int $value
     *
     * @return self
     */
    public function setSpaceBefore($value = null)
    {
        return $this->setSpace(['before' => $value]);
    }

    /**
     * Get space after paragraph.
     *
     * @return null|float|int
     */
    public function getSpaceAfter()
    {
        return $this->getChildStyleValue($this->spacing, 'after');
    }

    /**
     * Set space after paragraph.
     *
     * @param null|float|int $value
     *
     * @return self
     */
    public function setSpaceAfter($value = null)
    {
        return $this->setSpace(['after' => $value]);
    }

    /**
     * Get spacing between lines.
     *
     * @return null|float|int
     */
    public function getSpacing()
    {
        return $this->getChildStyleValue($this->spacing, 'line');
    }

    /**
     * Set spacing between lines.
     *
     * @param null|float|int $value
     *
     * @return self
     */
    public function setSpacing($value = null)
    {
        return $this->setSpace(['line' => $value]);
    }

    /**
     * Get spacing line rule.
     *
     * @return string
     */
    public function getSpacingLineRule()
    {
        return $this->getChildStyleValue($this->spacing, 'lineRule');
    }

    /**
     * Set the spacing line rule.
     *
     * @param string $value Possible values are defined in LineSpacingRule
     *
     * @return Paragraph
     */
    public function setSpacingLineRule($value)
    {
        return $this->setSpace(['lineRule' => $value]);
    }

    /**
     * Get line height.
     *
     * @return null|float|int
     */
    public function getLineHeight()
    {
        return $this->lineHeight;
    }

    /**
     * Set the line height.
     *
     * @param float|int|string $lineHeight
     *
     * @return self
     */
    public function setLineHeight($lineHeight)
    {
        if (is_string($lineHeight)) {
            $lineHeight = (float) (preg_replace('/[^0-9\.\,]/', '', $lineHeight));
        }

        if ((!is_int($lineHeight) && !is_float($lineHeight)) || !$lineHeight) {
            throw new InvalidStyleException('Line height must be a valid number');
        }

        $this->lineHeight = $lineHeight;
        $this->setSpacing(($lineHeight - 1) * self::LINE_HEIGHT);
        $this->setSpacingLineRule(\PhpOffice\PhpWord\SimpleType\LineSpacingRule::AUTO);

        return $this;
    }

    /**
     * Get allow first/last line to display on a separate page setting.
     *
     * @return bool
     */
    public function hasWidowControl()
    {
        return $this->widowControl;
    }

    /**
     * Set keep paragraph with next paragraph setting.
     *
     * @param bool $value
     *
     * @return self
     */
    public function setWidowControl($value = true)
    {
        $this->widowControl = $this->setBoolVal($value, $this->widowControl);

        return $this;
    }

    /**
     * Get keep paragraph with next paragraph setting.
     *
     * @return bool
     */
    public function isKeepNext()
    {
        return $this->keepNext;
    }

    /**
     * Set keep paragraph with next paragraph setting.
     *
     * @param bool $value
     *
     * @return self
     */
    public function setKeepNext($value = true)
    {
        $this->keepNext = $this->setBoolVal($value, $this->keepNext);

        return $this;
    }

    /**
     * Get keep all lines on one page setting.
     *
     * @return bool
     */
    public function isKeepLines()
    {
        return $this->keepLines;
    }

    /**
     * Set keep all lines on one page setting.
     *
     * @param bool $value
     *
     * @return self
     */
    public function setKeepLines($value = true)
    {
        $this->keepLines = $this->setBoolVal($value, $this->keepLines);

        return $this;
    }

    /**
     * Get start paragraph on next page setting.
     *
     * @return bool
     */
    public function hasPageBreakBefore()
    {
        return $this->pageBreakBefore;
    }

    /**
     * Set start paragraph on next page setting.
     *
     * @param bool $value
     *
     * @return self
     */
    public function setPageBreakBefore($value = true)
    {
        $this->pageBreakBefore = $this->setBoolVal($value, $this->pageBreakBefore);

        return $this;
    }

    /**
     * Get numbering style name.
     *
     * @return string
     */
    public function getNumStyle()
    {
        return $this->numStyle;
    }

    /**
     * Set numbering style name.
     *
     * @param string $value
     *
     * @return self
     */
    public function setNumStyle($value)
    {
        $this->numStyle = $value;

        return $this;
    }

    /**
     * Get numbering level.
     *
     * @return int
     */
    public function getNumLevel()
    {
        return $this->numLevel;
    }

    /**
     * Set numbering level.
     *
     * @param int $value
     *
     * @return self
     */
    public function setNumLevel($value = 0)
    {
        $this->numLevel = $this->setIntVal($value, $this->numLevel);

        return $this;
    }

    /**
     * Get tabs.
     *
     * @return Tab[]
     */
    public function getTabs()
    {
        return $this->tabs;
    }

    /**
     * Set tabs.
     *
     * @param array $value
     *
     * @return self
     */
    public function setTabs($value = null)
    {
        if (is_array($value)) {
            $this->tabs = $value;
        }

        return $this;
    }

    /**
     * Get shading.
     *
     * @return Shading
     */
    public function getShading()
    {
        return $this->shading;
    }

    /**
     * Set shading.
     *
     * @param mixed $value
     *
     * @return self
     */
    public function setShading($value = null)
    {
        $this->setObjectVal($value, 'Shading', $this->shading);

        return $this;
    }

    /**
     * Get contextualSpacing.
     *
     * @return bool
     */
    public function hasContextualSpacing()
    {
        return $this->contextualSpacing;
    }

    /**
     * Set contextualSpacing.
     *
     * @param bool $contextualSpacing
     *
     * @return self
     */
    public function setContextualSpacing($contextualSpacing)
    {
        $this->contextualSpacing = $contextualSpacing;

        return $this;
    }

    /**
     * Get bidirectional.
     *
     * @return ?bool
     */
    public function isBidi()
    {
        return $this->bidi ?? Settings::isDefaultRtl();
    }

    /**
     * Set bidi.
     *
     * @param ?bool $bidi
     *            Set to true to write from right to left
     *
     * @return self
     */
    public function setBidi($bidi)
    {
        $this->bidi = $bidi;

        return $this;
    }

    /**
     * Get textAlignment.
     *
     * @return string
     */
    public function getTextAlignment()
    {
        return $this->textAlignment;
    }

    /**
     * Set textAlignment.
     *
     * @param string $textAlignment
     *
     * @return self
     */
    public function setTextAlignment($textAlignment)
    {
        TextAlignment::validate($textAlignment);
        $this->textAlignment = $textAlignment;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasSuppressAutoHyphens()
    {
        return $this->suppressAutoHyphens;
    }

    /**
     * @param bool $suppressAutoHyphens
     */
    public function setSuppressAutoHyphens($suppressAutoHyphens): void
    {
        $this->suppressAutoHyphens = (bool) $suppressAutoHyphens;
    }
}
