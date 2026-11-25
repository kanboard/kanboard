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

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\Writer\WriterInterface;

/**
 * MPDF writer.
 *
 * @see  http://www.mpdf1.com/
 * @since 0.11.0
 */
class MPDF extends AbstractRenderer implements WriterInterface
{
    public const SIMULATED_BODY_START = '<!-- simulated body start -->';
    private const BODY_TAG = '<body>';

    /**
     * Overridden to set the correct includefile, only needed for MPDF 5.
     *
     * @codeCoverageIgnore
     */
    public function __construct(PhpWord $phpWord)
    {
        if (file_exists(Settings::getPdfRendererPath() . '/mpdf.php')) {
            // MPDF version 5.* needs this file to be included, later versions not
            $this->includeFile = 'mpdf.php';
        }
        parent::__construct($phpWord);
    }

    /**
     * Gets the implementation of external PDF library that should be used.
     *
     * @return \Mpdf\Mpdf implementation
     */
    protected function createExternalWriterInstance()
    {
        $mPdfClass = $this->getMPdfClassName();

        $options = [];
        if ($this->getFont()) {
            $options['default_font'] = $this->getFont();
        }

        return new $mPdfClass($options);
    }

    /**
     * Save PhpWord to file.
     */
    public function save(string $filename): void
    {
        $fileHandle = parent::prepareForSave($filename);

        //  PDF settings
        $paperSize = strtoupper('A4');
        $orientation = strtoupper('portrait');

        //  Create PDF
        $pdf = $this->createExternalWriterInstance();
        $pdf->_setPageSize($paperSize, $orientation);
        $pdf->addPage($orientation);

        // Write document properties
        $phpWord = $this->getPhpWord();
        $docProps = $phpWord->getDocInfo();
        $pdf->setTitle($docProps->getTitle());
        $pdf->setAuthor($docProps->getCreator());
        $pdf->setSubject($docProps->getSubject());
        $pdf->setKeywords($docProps->getKeywords());
        $pdf->setCreator($docProps->getCreator());

        $html = $this->getContent();
        $bodyLocation = strpos($html, self::SIMULATED_BODY_START);
        if ($bodyLocation === false) {
            $bodyLocation = strpos($html, self::BODY_TAG);
            if ($bodyLocation !== false) {
                $bodyLocation += strlen(self::BODY_TAG);
            }
        }
        // Make sure first data presented to Mpdf includes body tag
        //   (and any htmlpageheader/htmlpagefooter tags)
        //   so that Mpdf doesn't parse it as content. Issue 2432.
        if ($bodyLocation !== false) {
            $pdf->WriteHTML(substr($html, 0, $bodyLocation));
            $html = substr($html, $bodyLocation);
        }
        foreach (explode("\n", $html) as $line) {
            $pdf->WriteHTML("$line\n");
        }

        //  Write to file
        fwrite($fileHandle, $pdf->output($filename, 'S'));

        parent::restoreStateAfterSave($fileHandle);
    }

    /**
     * Return classname of MPDF to instantiate.
     *
     * @codeCoverageIgnore
     *
     * @return string
     */
    private function getMPdfClassName()
    {
        if ($this->includeFile != null) {
            // MPDF version 5.*
            return '\mpdf';
        }

        // MPDF version > 6.*
        return '\Mpdf\Mpdf';
    }
}
