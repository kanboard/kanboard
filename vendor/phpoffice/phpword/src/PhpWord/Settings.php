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

namespace PhpOffice\PhpWord;

/**
 * PHPWord settings class.
 *
 * @since 0.8.0
 */
class Settings
{
    /**
     * Zip libraries.
     *
     * @const string
     */
    public const ZIPARCHIVE = 'ZipArchive';
    public const PCLZIP = 'PclZip';
    public const OLD_LIB = Shared\ZipArchive::class; // @deprecated 0.11

    /**
     * PDF rendering libraries.
     *
     * @const string
     */
    public const PDF_RENDERER_DOMPDF = 'DomPDF';
    public const PDF_RENDERER_TCPDF = 'TCPDF';
    public const PDF_RENDERER_MPDF = 'MPDF';

    /**
     * Measurement units multiplication factor.
     * Applied to:
     * - Section: margins, header/footer height, gutter, column spacing
     * - Tab: position
     * - Indentation: left, right, firstLine, hanging
     * - Spacing: before, after.
     *
     * @const string
     */
    public const UNIT_TWIP = 'twip'; // = 1/20 point
    public const UNIT_CM = 'cm';
    public const UNIT_MM = 'mm';
    public const UNIT_INCH = 'inch';
    public const UNIT_POINT = 'point'; // = 1/72 inch
    public const UNIT_PICA = 'pica'; // = 1/6 inch = 12 points

    /**
     * Default font settings.
     * OOXML defined font size values in halfpoints, i.e. twice of what PhpWord
     * use, and the conversion will be conducted during XML writing.
     */
    public const DEFAULT_FONT_NAME = 'Arial';
    public const DEFAULT_FONT_SIZE = 10;
    public const DEFAULT_FONT_COLOR = '000000';
    public const DEFAULT_FONT_CONTENT_TYPE = 'default'; // default|eastAsia|cs
    public const DEFAULT_PAPER = 'A4';

    /**
     * Compatibility option for XMLWriter.
     *
     * @var bool
     */
    private static $xmlWriterCompatibility = true;

    /**
     * Name of the class used for Zip file management.
     *
     * @var string
     */
    private static $zipClass = self::ZIPARCHIVE;

    /**
     * Name of the external Library used for rendering PDF files.
     *
     * @var null|string
     */
    private static $pdfRendererName;

    /**
     * Options used for rendering PDF files.
     *
     * @var array
     */
    private static $pdfRendererOptions = [];

    /**
     * Directory Path to the external Library used for rendering PDF files.
     *
     * @var null|string
     */
    private static $pdfRendererPath;

    /**
     * Measurement unit.
     *
     * @var string
     */
    private static $measurementUnit = self::UNIT_TWIP;

    /**
     * Default font name.
     *
     * @var string
     */
    private static $defaultFontName = self::DEFAULT_FONT_NAME;

    /**
     * Default asian font name.
     *
     * @var string
     */
    private static $defaultAsianFontName = self::DEFAULT_FONT_NAME;

    /**
     * Default font color.
     *
     * @var string
     */
    private static $defaultFontColor = self::DEFAULT_FONT_COLOR;

    /**
     * Default font size.
     *
     * @var float|int
     */
    private static $defaultFontSize = self::DEFAULT_FONT_SIZE;

    /**
     * Default paper.
     *
     * @var string
     */
    private static $defaultPaper = self::DEFAULT_PAPER;

    /**
     * Is RTL by default ?
     *
     * @var ?bool
     */
    private static $defaultRtl;

    /**
     * The user defined temporary directory.
     *
     * @var string
     */
    private static $tempDir = '';

    /**
     * Enables built-in output escaping mechanism.
     * Default value is `false` for backward compatibility with versions below 0.13.0.
     *
     * @var bool
     */
    private static $outputEscapingEnabled = false;

    /**
     * Return the compatibility option used by the XMLWriter.
     *
     * @return bool Compatibility
     */
    public static function hasCompatibility(): bool
    {
        return self::$xmlWriterCompatibility;
    }

    /**
     * Set the compatibility option used by the XMLWriter.
     * This sets the setIndent and setIndentString for better compatibility.
     */
    public static function setCompatibility(bool $compatibility): bool
    {
        self::$xmlWriterCompatibility = $compatibility;

        return true;
    }

    /**
     * Get zip handler class.
     */
    public static function getZipClass(): string
    {
        return self::$zipClass;
    }

    /**
     * Set zip handler class.
     */
    public static function setZipClass(string $zipClass): bool
    {
        if (in_array($zipClass, [self::PCLZIP, self::ZIPARCHIVE, self::OLD_LIB])) {
            self::$zipClass = $zipClass;

            return true;
        }

        return false;
    }

    /**
     * Set details of the external library for rendering PDF files.
     *
     * @return bool Success or failure
     */
    public static function setPdfRenderer(string $libraryName, string $libraryBaseDir): bool
    {
        if (!self::setPdfRendererName($libraryName)) {
            return false;
        }

        return self::setPdfRendererPath($libraryBaseDir);
    }

    /**
     * Return the PDF Rendering Library.
     */
    public static function getPdfRendererName(): ?string
    {
        return self::$pdfRendererName;
    }

    /**
     * Identify the external library to use for rendering PDF files.
     */
    public static function setPdfRendererName(?string $libraryName): bool
    {
        $pdfRenderers = [self::PDF_RENDERER_DOMPDF, self::PDF_RENDERER_TCPDF, self::PDF_RENDERER_MPDF];
        if (!in_array($libraryName, $pdfRenderers)) {
            return false;
        }
        self::$pdfRendererName = $libraryName;

        return true;
    }

    /**
     * Return the directory path to the PDF Rendering Library.
     */
    public static function getPdfRendererPath(): ?string
    {
        return self::$pdfRendererPath;
    }

    /**
     * Set options of the external library for rendering PDF files.
     */
    public static function setPdfRendererOptions(array $options): void
    {
        self::$pdfRendererOptions = $options;
    }

    /**
     * Return the PDF Rendering Options.
     */
    public static function getPdfRendererOptions(): array
    {
        return self::$pdfRendererOptions;
    }

    /**
     * Location of external library to use for rendering PDF files.
     *
     * @param null|string $libraryBaseDir Directory path to the library's base folder
     *
     * @return bool Success or failure
     */
    public static function setPdfRendererPath(?string $libraryBaseDir): bool
    {
        if (!$libraryBaseDir || false === file_exists($libraryBaseDir) || false === is_readable($libraryBaseDir)) {
            return false;
        }
        self::$pdfRendererPath = $libraryBaseDir;

        return true;
    }

    /**
     * Get measurement unit.
     */
    public static function getMeasurementUnit(): string
    {
        return self::$measurementUnit;
    }

    /**
     * Set measurement unit.
     */
    public static function setMeasurementUnit(string $value): bool
    {
        $units = [
            self::UNIT_TWIP,
            self::UNIT_CM,
            self::UNIT_MM,
            self::UNIT_INCH,
            self::UNIT_POINT,
            self::UNIT_PICA,
        ];
        if (!in_array($value, $units)) {
            return false;
        }
        self::$measurementUnit = $value;

        return true;
    }

    /**
     * Sets the user defined path to temporary directory.
     *
     * @param string $tempDir The user defined path to temporary directory
     *
     * @since 0.12.0
     */
    public static function setTempDir(string $tempDir): void
    {
        self::$tempDir = $tempDir;
    }

    /**
     * Returns path to temporary directory.
     *
     * @since 0.12.0
     */
    public static function getTempDir(): string
    {
        if (!empty(self::$tempDir)) {
            $tempDir = self::$tempDir;
        } else {
            $tempDir = sys_get_temp_dir();
        }

        return $tempDir;
    }

    /**
     * @since 0.13.0
     */
    public static function isOutputEscapingEnabled(): bool
    {
        return self::$outputEscapingEnabled;
    }

    /**
     * @since 0.13.0
     */
    public static function setOutputEscapingEnabled(bool $outputEscapingEnabled): void
    {
        self::$outputEscapingEnabled = $outputEscapingEnabled;
    }

    /**
     * Get default font name.
     */
    public static function getDefaultFontName(): string
    {
        return self::$defaultFontName;
    }

    /**
     * Get default font name.
     */
    public static function getDefaultAsianFontName(): string
    {
        return self::$defaultAsianFontName;
    }

    /**
     * Set default font name.
     */
    public static function setDefaultFontName(string $value): bool
    {
        if (trim($value) !== '') {
            self::$defaultFontName = $value;

            return true;
        }

        return false;
    }

    public static function setDefaultAsianFontName(string $value): bool
    {
        if (trim($value) !== '') {
            self::$defaultAsianFontName = $value;

            return true;
        }

        return false;
    }

    /**
     * Get default font color.
     */
    public static function getDefaultFontColor(): string
    {
        return self::$defaultFontColor;
    }

    /**
     * Set default font color.
     */
    public static function setDefaultFontColor(string $value): bool
    {
        if (trim($value) !== '') {
            self::$defaultFontColor = $value;

            return true;
        }

        return false;
    }

    /**
     * Get default font size.
     *
     * @return float|int
     */
    public static function getDefaultFontSize()
    {
        return self::$defaultFontSize;
    }

    /**
     * Set default font size.
     *
     * @param null|float|int $value
     */
    public static function setDefaultFontSize($value): bool
    {
        if ((is_int($value) || is_float($value)) && (int) $value > 0) {
            self::$defaultFontSize = $value;

            return true;
        }

        return false;
    }

    public static function setDefaultRtl(?bool $defaultRtl): void
    {
        self::$defaultRtl = $defaultRtl;
    }

    public static function isDefaultRtl(): ?bool
    {
        return self::$defaultRtl;
    }

    /**
     * Load setting from phpword.yml or phpword.yml.dist.
     */
    public static function loadConfig(?string $filename = null): array
    {
        // Get config file
        $configFile = null;
        $configPath = __DIR__ . '/../../';
        if ($filename !== null) {
            $files = [$filename];
        } else {
            $files = ["{$configPath}phpword.ini", "{$configPath}phpword.ini.dist"];
        }
        foreach ($files as $file) {
            if (file_exists($file)) {
                $configFile = realpath($file);

                break;
            }
        }

        // Parse config file
        $config = [];
        if ($configFile !== null) {
            $config = @parse_ini_file($configFile);
            if ($config === false) {
                return [];
            }
        }

        // Set config value
        $appliedConfig = [];
        foreach ($config as $key => $value) {
            $method = "set{$key}";
            if (method_exists(__CLASS__, $method)) {
                self::$method($value);
                $appliedConfig[$key] = $value;
            }
        }

        return $appliedConfig;
    }

    /**
     * Get default paper.
     */
    public static function getDefaultPaper(): string
    {
        return self::$defaultPaper;
    }

    /**
     * Set default paper.
     */
    public static function setDefaultPaper(string $value): bool
    {
        if (trim($value) !== '') {
            self::$defaultPaper = $value;

            return true;
        }

        return false;
    }
}
