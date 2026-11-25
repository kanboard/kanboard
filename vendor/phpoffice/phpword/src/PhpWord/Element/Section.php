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

use Exception;
use PhpOffice\PhpWord\ComplexType\FootnoteProperties;
use PhpOffice\PhpWord\Style\Section as SectionStyle;

class Section extends AbstractContainer
{
    /**
     * @var string Container type
     */
    protected $container = 'Section';

    /**
     * Section style.
     *
     * @var ?SectionStyle
     */
    private $style;

    /**
     * Section headers, indexed from 1, not zero.
     *
     * @var Header[]
     */
    private $headers = [];

    /**
     * Section footers, indexed from 1, not zero.
     *
     * @var Footer[]
     */
    private $footers = [];

    /**
     * The properties for the footnote of this section.
     *
     * @var FootnoteProperties
     */
    private $footnoteProperties;

    /**
     * Create new instance.
     *
     * @param int $sectionCount
     * @param null|array|\PhpOffice\PhpWord\Style|string $style
     */
    public function __construct($sectionCount, $style = null)
    {
        $this->sectionId = $sectionCount;
        $this->setDocPart($this->container, $this->sectionId);
        if (null === $style) {
            $style = new SectionStyle();
        }
        $this->style = $this->setNewStyle(new SectionStyle(), $style);
    }

    /**
     * Set section style.
     *
     * @param array $style
     */
    public function setStyle($style = null): void
    {
        if (null !== $style && is_array($style)) {
            $this->style->setStyleByArray($style);
        }
    }

    /**
     * Get section style.
     *
     * @return ?SectionStyle
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * Add header.
     *
     * @since 0.10.0
     *
     * @param string $type
     *
     * @return Header
     */
    public function addHeader($type = Header::AUTO)
    {
        return $this->addHeaderFooter($type, true);
    }

    /**
     * Add footer.
     *
     * @since 0.10.0
     *
     * @param string $type
     *
     * @return Footer
     */
    public function addFooter($type = Header::AUTO)
    {
        return $this->addHeaderFooter($type, false);
    }

    /**
     * Get header elements.
     *
     * @return Header[]
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Get footer elements.
     *
     * @return Footer[]
     */
    public function getFooters()
    {
        return $this->footers;
    }

    /**
     * Get the footnote properties.
     *
     * @return FootnoteProperties
     */
    public function getFootnoteProperties()
    {
        return $this->footnoteProperties;
    }

    /**
     * Set the footnote properties.
     */
    public function setFootnoteProperties(?FootnoteProperties $footnoteProperties = null): void
    {
        $this->footnoteProperties = $footnoteProperties;
    }

    /**
     * Is there a header for this section that is for the first page only?
     *
     * If any of the Header instances have a type of Header::FIRST then this method returns true.
     * False otherwise.
     *
     * @return bool
     */
    public function hasDifferentFirstPage()
    {
        foreach ($this->headers as $header) {
            if ($header->getType() == Header::FIRST) {
                return true;
            }
        }
        foreach ($this->footers as $footer) {
            if ($footer->getType() == Header::FIRST) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add header/footer.
     *
     * @since 0.10.0
     *
     * @param string $type
     * @param bool $header
     *
     * @return Footer|Header
     */
    private function addHeaderFooter($type = Header::AUTO, $header = true)
    {
        $containerClass = substr(static::class, 0, strrpos(static::class, '\\') ?: 0) . '\\' .
            ($header ? 'Header' : 'Footer');
        $collectionArray = $header ? 'headers' : 'footers';
        $collection = &$this->$collectionArray;

        if (in_array($type, [Header::AUTO, Header::FIRST, Header::EVEN])) {
            $index = count($collection);
            /** @var AbstractContainer $container Type hint */
            $container = new $containerClass($this->sectionId, ++$index, $type);
            $container->setPhpWord($this->phpWord);

            $collection[$index] = $container;

            return $container;
        }

        throw new Exception('Invalid header/footer type.');
    }
}
