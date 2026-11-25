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
 * @see         https://github.com/PHPOffice/PhpWord
 *
 * @license     http://www.gnu.org/licenses/lgpl.txt LGPL version 3
 */

namespace PhpOffice\PhpWord\Writer\PDF;

use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\Writer\HTML;

/**
 * Abstract PDF renderer.
 *
 * @since 0.10.0
 */
abstract class AbstractRenderer extends HTML
{
    /**
     * Name of renderer include file.
     *
     * @var string
     */
    protected $includeFile;

    /**
     * Temporary storage directory.
     *
     * @var string
     */
    protected $tempDir = '';

    /**
     * Font.
     *
     * @var string
     */
    protected $font;

    /**
     * Paper size.
     *
     * @var int
     */
    protected $paperSize;

    /**
     * Orientation.
     *
     * @var string
     */
    protected $orientation;

    /**
     * Paper Sizes xRef List.
     *
     * @var array
     */
    protected static $paperSizes = [
        9 => 'A4', // (210 mm by 297 mm)
    ];

    /**
     * Create new instance.
     *
     * @param PhpWord $phpWord PhpWord object
     */
    public function __construct(PhpWord $phpWord)
    {
        parent::__construct($phpWord);
        $this->isPdf = true;
        if ($this->includeFile != null) {
            $includeFile = Settings::getPdfRendererPath() . '/' . $this->includeFile;
            if (file_exists($includeFile)) {
                /** @noinspection PhpIncludeInspection Dynamic includes */
                require_once $includeFile;
            } else {
                // @codeCoverageIgnoreStart
                // Can't find any test case. Uncomment when found.
                throw new Exception('Unable to load PDF Rendering library');
                // @codeCoverageIgnoreEnd
            }
        }

        // Configuration
        $options = Settings::getPdfRendererOptions();
        if (!empty($options['font'])) {
            $this->setFont($options['font']);
        }
    }

    /**
     * Get Font.
     *
     * @return string
     */
    public function getFont()
    {
        return $this->font;
    }

    /**
     * Set font. Examples:
     *      'arialunicid0-chinese-simplified'
     *      'arialunicid0-chinese-traditional'
     *      'arialunicid0-korean'
     *      'arialunicid0-japanese'.
     *
     * @param string $fontName
     *
     * @return self
     */
    public function setFont($fontName)
    {
        $this->font = $fontName;

        return $this;
    }

    /**
     * Get Paper Size.
     *
     * @return int
     */
    public function getPaperSize()
    {
        return $this->paperSize;
    }

    /**
     * Set Paper Size.
     *
     * @param int $value Paper size = PAPERSIZE_A4
     *
     * @return self
     */
    public function setPaperSize($value = 9)
    {
        $this->paperSize = $value;

        return $this;
    }

    /**
     * Get Orientation.
     *
     * @return string
     */
    public function getOrientation()
    {
        return $this->orientation;
    }

    /**
     * Set Orientation.
     *
     * @param string $value Page orientation ORIENTATION_DEFAULT
     *
     * @return self
     */
    public function setOrientation($value = 'default')
    {
        $this->orientation = $value;

        return $this;
    }

    /**
     * Save PhpWord to PDF file, pre-save.
     *
     * @param string $filename Name of the file to save as
     *
     * @return resource
     */
    protected function prepareForSave($filename = null)
    {
        $fileHandle = fopen($filename, 'wb');
        // @codeCoverageIgnoreStart
        // Can't find any test case. Uncomment when found.
        if ($fileHandle === false) {
            throw new Exception("Could not open file $filename for writing.");
        }
        // @codeCoverageIgnoreEnd

        return $fileHandle;
    }

    /**
     * Save PhpWord to PDF file, post-save.
     *
     * @param resource $fileHandle
     */
    protected function restoreStateAfterSave($fileHandle): void
    {
        fclose($fileHandle);
    }
}
