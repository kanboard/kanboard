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

use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\Shared\Validate;

/**
 * Font style.
 */
class Font extends AbstractStyle
{
    /**
     * Underline types.
     *
     * @const string
     */
    const UNDERLINE_NONE = 'none';
    const UNDERLINE_DASH = 'dash';
    const UNDERLINE_DASHHEAVY = 'dashHeavy';
    const UNDERLINE_DASHLONG = 'dashLong';
    const UNDERLINE_DASHLONGHEAVY = 'dashLongHeavy';
    const UNDERLINE_DOUBLE = 'dbl';
    const UNDERLINE_DOTDASH = 'dotDash';
    const UNDERLINE_DOTDASHHEAVY = 'dotDashHeavy';
    const UNDERLINE_DOTDOTDASH = 'dotDotDash';
    const UNDERLINE_DOTDOTDASHHEAVY = 'dotDotDashHeavy';
    const UNDERLINE_DOTTED = 'dotted';
    const UNDERLINE_DOTTEDHEAVY = 'dottedHeavy';
    const UNDERLINE_HEAVY = 'heavy';
    const UNDERLINE_SINGLE = 'single';
    const UNDERLINE_WAVY = 'wavy';
    const UNDERLINE_WAVYDOUBLE = 'wavyDbl';
    const UNDERLINE_WAVYHEAVY = 'wavyHeavy';
    const UNDERLINE_WORDS = 'words';

    /**
     * Foreground colors.
     *
     * @const string
     */
    const FGCOLOR_YELLOW = 'yellow';
    const FGCOLOR_LIGHTGREEN = 'green';
    const FGCOLOR_CYAN = 'cyan';
    const FGCOLOR_MAGENTA = 'magenta';
    const FGCOLOR_BLUE = 'blue';
    const FGCOLOR_RED = 'red';
    const FGCOLOR_DARKBLUE = 'darkBlue';
    const FGCOLOR_DARKCYAN = 'darkCyan';
    const FGCOLOR_DARKGREEN = 'darkGreen';
    const FGCOLOR_DARKMAGENTA = 'darkMagenta';
    const FGCOLOR_DARKRED = 'darkRed';
    const FGCOLOR_DARKYELLOW = 'darkYellow';
    const FGCOLOR_DARKGRAY = 'darkGray';
    const FGCOLOR_LIGHTGRAY = 'lightGray';
    const FGCOLOR_BLACK = 'black';

    /**
     * Aliases.
     *
     * @var array
     */
    protected $aliases = ['line-height' => 'lineHeight', 'letter-spacing' => 'spacing'];

    /**
     * Font style type.
     *
     * @var string
     */
    private $type;

    /**
     * Font name.
     *
     * @var string
     */
    private $name;

    /**
     * Font Content Type.
     *
     * @var string
     */
    private $hint;

    /**
     * Font size.
     *
     * @var float|int
     */
    private $size;

    /**
     * Font color.
     *
     * @var null|string
     */
    private $color;

    /**
     * Bold.
     *
     * @var bool
     */
    private $bold;

    /**
     * Italic.
     *
     * @var bool
     */
    private $italic;

    /**
     * Undeline.
     *
     * @var string
     */
    private $underline = self::UNDERLINE_NONE;

    /**
     * Superscript.
     *
     * @var bool
     */
    private $superScript = false;

    /**
     * Subscript.
     *
     * @var bool
     */
    private $subScript = false;

    /**
     * Strikethrough.
     *
     * @var bool
     */
    private $strikethrough;

    /**
     * Double strikethrough.
     *
     * @var bool
     */
    private $doubleStrikethrough;

    /**
     * Small caps.
     *
     * @var bool
     *
     * @see  http://www.schemacentral.com/sc/ooxml/e-w_smallCaps-1.html
     */
    private $smallCaps;

    /**
     * All caps.
     *
     * @var bool
     *
     * @see  http://www.schemacentral.com/sc/ooxml/e-w_caps-1.html
     */
    private $allCaps;

    /**
     * Foreground/highlight.
     *
     * @var string
     */
    private $fgColor;

    /**
     * Expanded/compressed text: 0-600 (percent).
     *
     * @var int
     *
     * @since 0.12.0
     * @see  http://www.schemacentral.com/sc/ooxml/e-w_w-1.html
     */
    private $scale;

    /**
     * Character spacing adjustment: twip.
     *
     * @var float|int
     *
     * @since 0.12.0
     * @see  http://www.schemacentral.com/sc/ooxml/e-w_spacing-2.html
     */
    private $spacing;

    /**
     * Font kerning: halfpoint.
     *
     * @var float|int
     *
     * @since 0.12.0
     * @see  http://www.schemacentral.com/sc/ooxml/e-w_kern-1.html
     */
    private $kerning;

    /**
     * Paragraph style.
     *
     * @var Paragraph
     */
    private $paragraph;

    /**
     * Shading.
     *
     * @var Shading
     */
    private $shading;

    /**
     * Right to left languages.
     *
     * @var ?bool
     */
    private $rtl;

    /**
     * noProof (disables AutoCorrect).
     *
     * @var bool
     * http://www.datypic.com/sc/ooxml/e-w_noProof-1.html
     */
    private $noProof;

    /**
     * Languages.
     *
     * @var null|Language
     */
    private $lang;

    /**
     * Hidden text.
     *
     * @var bool
     *
     * @see  http://www.datypic.com/sc/ooxml/e-w_vanish-1.html
     */
    private $hidden;

    /**
     * Vertically Raised or Lowered Text.
     *
     * @var int Signed Half-Point Measurement
     *
     * @see http://www.datypic.com/sc/ooxml/e-w_position-1.html
     */
    private $position;

    /**
     * Preservation of white space in html.
     *
     * @var string Value used for css white-space
     */
    private $whiteSpace = '';

    /**
     * Generic font as fallback for html.
     *
     * @var string generic font name
     */
    private $fallbackFont = '';

    /**
     * Create new font style.
     *
     * @param string $type Type of font
     * @param AbstractStyle|array|string $paragraph Paragraph styles definition
     */
    public function __construct($type = 'text', $paragraph = null)
    {
        $this->type = $type;
        $this->setParagraph($paragraph);
    }

    /**
     * Get style values.
     *
     * @return array
     *
     * @since 0.12.0
     */
    public function getStyleValues()
    {
        return [
            'name' => $this->getStyleName(),
            'basic' => [
                'name' => $this->getName(),
                'size' => $this->getSize(),
                'color' => $this->getColor(),
                'hint' => $this->getHint(),
            ],
            'style' => [
                'bold' => $this->isBold(),
                'italic' => $this->isItalic(),
                'underline' => $this->getUnderline(),
                'strike' => $this->isStrikethrough(),
                'dStrike' => $this->isDoubleStrikethrough(),
                'super' => $this->isSuperScript(),
                'sub' => $this->isSubScript(),
                'smallCaps' => $this->isSmallCaps(),
                'allCaps' => $this->isAllCaps(),
                'fgColor' => $this->getFgColor(),
                'hidden' => $this->isHidden(),
            ],
            'spacing' => [
                'scale' => $this->getScale(),
                'spacing' => $this->getSpacing(),
                'kerning' => $this->getKerning(),
                'position' => $this->getPosition(),
            ],
            'paragraph' => $this->getParagraph(),
            'rtl' => $this->isRTL(),
            'shading' => $this->getShading(),
            'lang' => $this->getLang(),
            'whiteSpace' => $this->getWhiteSpace(),
            'fallbackFont' => $this->getFallbackFont(),
        ];
    }

    /**
     * Get style type.
     *
     * @return string
     */
    public function getStyleType()
    {
        return $this->type;
    }

    /**
     * Get font name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set font name.
     *
     * @param string $value
     *
     * @return self
     */
    public function setName($value = null)
    {
        $this->name = $value;

        return $this;
    }

    /**
     * Get Font Content Type.
     *
     * @return string
     */
    public function getHint()
    {
        return $this->hint;
    }

    /**
     * Set Font Content Type.
     *
     * @param string $value
     *
     * @return self
     */
    public function setHint($value = null)
    {
        $this->hint = $value;

        return $this;
    }

    /**
     * Get font size.
     *
     * @return float|int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set font size.
     *
     * @param float|int $value
     *
     * @return self
     */
    public function setSize($value = null)
    {
        $this->size = $this->setNumericVal($value, $this->size);

        return $this;
    }

    /**
     * Get font color.
     */
    public function getColor(): ?string
    {
        return $this->color;
    }

    /**
     * Set font color.
     *
     * @param string $value
     *
     * @return self
     */
    public function setColor($value = null)
    {
        $this->color = $value;

        return $this;
    }

    /**
     * Get bold.
     *
     * @return bool
     */
    public function isBold()
    {
        return $this->bold;
    }

    /**
     * Set bold.
     *
     * @param bool $value
     *
     * @return self
     */
    public function setBold($value = true)
    {
        $this->bold = $this->setBoolVal($value, $this->bold);

        return $this;
    }

    /**
     * Get italic.
     *
     * @return bool
     */
    public function isItalic()
    {
        return $this->italic;
    }

    /**
     * Set italic.
     *
     * @param bool $value
     *
     * @return self
     */
    public function setItalic($value = true)
    {
        $this->italic = $this->setBoolVal($value, $this->italic);

        return $this;
    }

    /**
     * Get underline.
     *
     * @return string
     */
    public function getUnderline()
    {
        return $this->underline;
    }

    /**
     * Set underline.
     *
     * @param string $value
     *
     * @return self
     */
    public function setUnderline($value = self::UNDERLINE_NONE)
    {
        $this->underline = $this->setNonEmptyVal($value, self::UNDERLINE_NONE);

        return $this;
    }

    /**
     * Get superscript.
     *
     * @return bool
     */
    public function isSuperScript()
    {
        return $this->superScript;
    }

    /**
     * Set superscript.
     *
     * @param bool $value
     *
     * @return self
     */
    public function setSuperScript($value = true)
    {
        return $this->setPairedVal($this->superScript, $this->subScript, $value);
    }

    /**
     * Get subscript.
     *
     * @return bool
     */
    public function isSubScript()
    {
        return $this->subScript;
    }

    /**
     * Set subscript.
     *
     * @param bool $value
     *
     * @return self
     */
    public function setSubScript($value = true)
    {
        return $this->setPairedVal($this->subScript, $this->superScript, $value);
    }

    /**
     * Get strikethrough.
     */
    public function isStrikethrough(): ?bool
    {
        return $this->strikethrough;
    }

    /**
     * Set strikethrough.
     *
     * @param bool $value
     */
    public function setStrikethrough($value = true): self
    {
        return $this->setPairedVal($this->strikethrough, $this->doubleStrikethrough, $value);
    }

    /**
     * Get double strikethrough.
     */
    public function isDoubleStrikethrough(): ?bool
    {
        return $this->doubleStrikethrough;
    }

    /**
     * Set double strikethrough.
     *
     * @param bool $value
     */
    public function setDoubleStrikethrough($value = true): self
    {
        return $this->setPairedVal($this->doubleStrikethrough, $this->strikethrough, $value);
    }

    /**
     * Get small caps.
     *
     * @return bool
     */
    public function isSmallCaps()
    {
        return $this->smallCaps;
    }

    /**
     * Set small caps.
     *
     * @param bool $value
     *
     * @return self
     */
    public function setSmallCaps($value = true)
    {
        return $this->setPairedVal($this->smallCaps, $this->allCaps, $value);
    }

    /**
     * Get all caps.
     *
     * @return bool
     */
    public function isAllCaps()
    {
        return $this->allCaps;
    }

    /**
     * Set all caps.
     *
     * @param bool $value
     *
     * @return self
     */
    public function setAllCaps($value = true)
    {
        return $this->setPairedVal($this->allCaps, $this->smallCaps, $value);
    }

    /**
     * Get foreground/highlight color.
     *
     * @return string
     */
    public function getFgColor()
    {
        return $this->fgColor;
    }

    /**
     * Set foreground/highlight color.
     *
     * @param string $value
     *
     * @return self
     */
    public function setFgColor($value = null)
    {
        $this->fgColor = $value;

        return $this;
    }

    /**
     * Get background.
     *
     * @return string
     */
    public function getBgColor()
    {
        return $this->getChildStyleValue($this->shading, 'fill');
    }

    /**
     * Set background.
     *
     * @param string $value
     *
     * @return Table
     */
    public function setBgColor($value = null)
    {
        $this->setShading(['fill' => $value]);
    }

    /**
     * Get scale.
     *
     * @return int
     */
    public function getScale()
    {
        return $this->scale;
    }

    /**
     * Set scale.
     *
     * @param int $value
     *
     * @return self
     */
    public function setScale($value = null)
    {
        $this->scale = $this->setIntVal($value, null);

        return $this;
    }

    /**
     * Get font spacing.
     *
     * @return float|int
     */
    public function getSpacing()
    {
        return $this->spacing;
    }

    /**
     * Set font spacing.
     *
     * @param float|int $value
     *
     * @return self
     */
    public function setSpacing($value = null)
    {
        $this->spacing = $this->setNumericVal($value, null);

        return $this;
    }

    /**
     * Get font kerning.
     *
     * @return float|int
     */
    public function getKerning()
    {
        return $this->kerning;
    }

    /**
     * Set font kerning.
     *
     * @param float|int $value
     *
     * @return self
     */
    public function setKerning($value = null)
    {
        $this->kerning = $this->setNumericVal($value, null);

        return $this;
    }

    /**
     * Get noProof (disables autocorrect).
     *
     * @return bool
     */
    public function isNoProof()
    {
        return $this->noProof;
    }

    /**
     * Set noProof (disables autocorrect).
     *
     * @param bool $value
     *
     * @return $this
     */
    public function setNoProof($value = false)
    {
        $this->noProof = $value;

        return $this;
    }

    /**
     * Get line height.
     *
     * @return float|int
     */
    public function getLineHeight()
    {
        return $this->getParagraph()->getLineHeight();
    }

    /**
     * Set lineheight.
     *
     * @param float|int|string $value
     *
     * @return self
     */
    public function setLineHeight($value)
    {
        $this->setParagraph(['lineHeight' => $value]);

        return $this;
    }

    /**
     * Get paragraph style.
     *
     * @return Paragraph
     */
    public function getParagraph()
    {
        return $this->paragraph;
    }

    /**
     * Set Paragraph.
     *
     * @param mixed $value
     *
     * @return self
     */
    public function setParagraph($value = null)
    {
        $this->setObjectVal($value, 'Paragraph', $this->paragraph);

        return $this;
    }

    /**
     * Get rtl.
     *
     * @return ?bool
     */
    public function isRTL()
    {
        return $this->rtl ?? Settings::isDefaultRtl();
    }

    /**
     * Set rtl.
     *
     * @param ?bool $value
     *
     * @return self
     */
    public function setRTL($value = true)
    {
        $this->rtl = $this->setBoolVal($value, $this->rtl);

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
     * Get language.
     *
     * @return null|Language
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * Set language.
     *
     * @param mixed $value
     *
     * @return self
     */
    public function setLang($value = null)
    {
        if (is_string($value) && $value != '') {
            $value = new Language($value);
        }
        $this->setObjectVal($value, 'Language', $this->lang);

        return $this;
    }

    /**
     * Get hidden text.
     *
     * @return bool
     */
    public function isHidden()
    {
        return $this->hidden;
    }

    /**
     * Set hidden text.
     *
     * @param bool $value
     *
     * @return self
     */
    public function setHidden($value = true)
    {
        $this->hidden = $this->setBoolVal($value, $this->hidden);

        return $this;
    }

    /**
     * Get position.
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set position.
     *
     * @param int $value
     *
     * @return self
     */
    public function setPosition($value = null)
    {
        $this->position = $this->setIntVal($value, null);

        return $this;
    }

    /**
     * Set html css white-space value. It is expected that only pre-wrap and normal (default) are useful.
     *
     * @param null|string $value Should be one of pre-wrap, normal, nowrap, pre, pre-line, initial, inherit
     */
    public function setWhiteSpace(?string $value): self
    {
        $this->whiteSpace = Validate::validateCSSWhiteSpace($value);

        return $this;
    }

    /**
     * Get html css white-space value.
     */
    public function getWhiteSpace(): string
    {
        return $this->whiteSpace;
    }

    /**
     * Set generic font for fallback for html.
     *
     * @param string $value generic font name
     */
    public function setFallbackFont(?string $value): self
    {
        $this->fallbackFont = Validate::validateCSSGenericFont($value);

        return $this;
    }

    /**
     * Get html fallback generic font.
     */
    public function getFallbackFont(): string
    {
        return $this->fallbackFont;
    }
}
