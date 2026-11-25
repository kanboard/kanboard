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

namespace PhpOffice\PhpWord\Writer;

use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\Writer\PDF\AbstractRenderer;

/**
 * PDF Writer.
 *
 * @since 0.10.0
 */
class PDF
{
    /**
     * The wrapper for the requested PDF rendering engine.
     *
     * @var AbstractRenderer
     */
    private $renderer;

    /**
     * Instantiate a new renderer of the configured type within this container class.
     */
    public function __construct(PhpWord $phpWord)
    {
        $pdfLibraryName = Settings::getPdfRendererName();
        $pdfLibraryPath = Settings::getPdfRendererPath();
        if (null === $pdfLibraryName || null === $pdfLibraryPath) {
            throw new Exception('PDF rendering library or library path has not been defined.');
        }

        $includePath = str_replace('\\', '/', get_include_path());
        $rendererPath = str_replace('\\', '/', $pdfLibraryPath);
        if (strpos($rendererPath, $includePath) === false) {
            set_include_path(get_include_path() . PATH_SEPARATOR . $pdfLibraryPath);
        }

        $rendererName = static::class . '\\' . $pdfLibraryName;
        $this->renderer = new $rendererName($phpWord);
    }

    /**
     * Magic method to handle direct calls to the configured PDF renderer wrapper class.
     *
     * @param string $name Renderer library method name
     * @param mixed[] $arguments Array of arguments to pass to the renderer method
     *
     * @return mixed Returned data from the PDF renderer wrapper method
     */
    public function __call($name, $arguments)
    {
        // Note: Commented because all exceptions should already be catched by `__construct`
        // if ($this->renderer === null) {
        //     throw new Exception("PDF Rendering library has not been defined.");
        // }

        return call_user_func_array([$this->getRenderer(), $name], $arguments);
    }

    public function save(string $filename): void
    {
        $this->getRenderer()->save($filename);
    }

    public function getRenderer(): AbstractRenderer
    {
        return $this->renderer;
    }
}
