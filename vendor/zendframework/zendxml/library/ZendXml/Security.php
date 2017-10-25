<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ZendXml;

use DOMDocument;
use SimpleXMLElement;

class Security
{
    const ENTITY_DETECT = 'Detected use of ENTITY in XML, disabled to prevent XXE/XEE attacks';

    /**
     * Heuristic scan to detect entity in XML
     *
     * @param  string $xml
     * @throws Exception\RuntimeException If entity expansion or external entity declaration was discovered.
     */
    protected static function heuristicScan($xml)
    {
        foreach (self::getEntityComparison($xml) as $compare) {
            if (strpos($xml, $compare) !== false) {
                throw new Exception\RuntimeException(self::ENTITY_DETECT);
            }
        }
    }

    /**
     * Scan XML string for potential XXE and XEE attacks
     *
     * @param   string $xml
     * @param   DomDocument $dom
     * @throws  Exception\RuntimeException
     * @return  SimpleXMLElement|DomDocument|boolean
     */
    public static function scan($xml, DOMDocument $dom = null)
    {
        // If running with PHP-FPM we perform an heuristic scan
        // We cannot use libxml_disable_entity_loader because of this bug
        // @see https://bugs.php.net/bug.php?id=64938
        if (self::isPhpFpm()) {
            self::heuristicScan($xml);
        }

        if (null === $dom) {
            $simpleXml = true;
            $dom = new DOMDocument();
        }

        if (!self::isPhpFpm()) {
            $loadEntities = libxml_disable_entity_loader(true);
            $useInternalXmlErrors = libxml_use_internal_errors(true);
        }

        // Load XML with network access disabled (LIBXML_NONET)
        // error disabled with @ for PHP-FPM scenario
        set_error_handler(function ($errno, $errstr) {
            if (substr_count($errstr, 'DOMDocument::loadXML()') > 0) {
                return true;
            }
            return false;
        }, E_WARNING);
        $result = $dom->loadXml($xml, LIBXML_NONET);
        restore_error_handler();

        if (!$result) {
            // Entity load to previous setting
            if (!self::isPhpFpm()) {
                libxml_disable_entity_loader($loadEntities);
                libxml_use_internal_errors($useInternalXmlErrors);
            }
            return false;
        }

        // Scan for potential XEE attacks using ENTITY, if not PHP-FPM
        if (!self::isPhpFpm()) {
            foreach ($dom->childNodes as $child) {
                if ($child->nodeType === XML_DOCUMENT_TYPE_NODE) {
                    if ($child->entities->length > 0) {
                        throw new Exception\RuntimeException(self::ENTITY_DETECT);
                    }
                }
            }
        }

        // Entity load to previous setting
        if (!self::isPhpFpm()) {
            libxml_disable_entity_loader($loadEntities);
            libxml_use_internal_errors($useInternalXmlErrors);
        }

        if (isset($simpleXml)) {
            $result = simplexml_import_dom($dom);
            if (!$result instanceof SimpleXMLElement) {
                return false;
            }
            return $result;
        }
        return $dom;
    }

    /**
     * Scan XML file for potential XXE/XEE attacks
     *
     * @param  string $file
     * @param  DOMDocument $dom
     * @throws Exception\InvalidArgumentException
     * @return SimpleXMLElement|DomDocument
     */
    public static function scanFile($file, DOMDocument $dom = null)
    {
        if (!file_exists($file)) {
            throw new Exception\InvalidArgumentException(
                "The file $file specified doesn't exist"
            );
        }
        return self::scan(file_get_contents($file), $dom);
    }

    /**
     * Return true if PHP is running with PHP-FPM
     *
     * This method is mainly used to determine whether or not heuristic checks
     * (vs libxml checks) should be made, due to threading issues in libxml;
     * under php-fpm, threading becomes a concern.
     *
     * However, PHP versions 5.5.22+ and 5.6.6+ contain a patch to the
     * libxml support in PHP that makes the libxml checks viable; in such
     * versions, this method will return false to enforce those checks, which
     * are more strict and accurate than the heuristic checks.
     *
     * @return boolean
     */
    public static function isPhpFpm()
    {
        $isVulnerableVersion = (
            version_compare(PHP_VERSION, '5.5.22', 'lt')
            || (
                version_compare(PHP_VERSION, '5.6', 'gte')
                && version_compare(PHP_VERSION, '5.6.6', 'lt')
            )
        );

        if (substr(php_sapi_name(), 0, 3) === 'fpm' && $isVulnerableVersion) {
            return true;
        }
        return false;
    }

    /**
     * Determine and return the string(s) to use for the <!ENTITY comparison.
     *
     * @param string $xml
     * @return string[]
     */
    protected static function getEntityComparison($xml)
    {
        $encodingMap = self::getAsciiEncodingMap();
        return array_map(function ($encoding) use ($encodingMap) {
            $generator   = isset($encodingMap[$encoding]) ? $encodingMap[$encoding] : $encodingMap['UTF-8'];
            return $generator('<!ENTITY');
        }, self::detectXmlEncoding($xml, self::detectStringEncoding($xml)));
    }

    /**
     * Determine the string encoding.
     *
     * Determines string encoding from either a detected BOM or a
     * heuristic.
     *
     * @param string $xml
     * @return string File encoding
     */
    protected static function detectStringEncoding($xml)
    {
        return self::detectBom($xml) ?: self::detectXmlStringEncoding($xml);
    }

    /**
     * Attempt to match a known BOM.
     *
     * Iterates through the return of getBomMap(), comparing the initial bytes
     * of the provided string to the BOM of each; if a match is determined,
     * it returns the encoding.
     *
     * @param string $string
     * @return false|string Returns encoding on success.
     */
    protected static function detectBom($string)
    {
        foreach (self::getBomMap() as $criteria) {
            if (0 === strncmp($string, $criteria['bom'], $criteria['length'])) {
                return $criteria['encoding'];
            }
        }
        return false;
    }

    /**
     * Attempt to detect the string encoding of an XML string.
     *
     * @param string $xml
     * @return string Encoding
     */
    protected static function detectXmlStringEncoding($xml)
    {
        foreach (self::getAsciiEncodingMap() as $encoding => $generator) {
            $prefix = $generator('<' . '?xml');
            if (0 === strncmp($xml, $prefix, strlen($prefix))) {
                return $encoding;
            }
        }

        // Fallback
        return 'UTF-8';
    }

    /**
     * Attempt to detect the specified XML encoding.
     *
     * Using the file's encoding, determines if an "encoding" attribute is
     * present and well-formed in the XML declaration; if so, it returns a
     * list with both the ASCII representation of that declaration and the
     * original file encoding.
     *
     * If not, a list containing only the provided file encoding is returned.
     *
     * @param string $xml
     * @param string $fileEncoding
     * @return string[] Potential XML encodings
     */
    protected static function detectXmlEncoding($xml, $fileEncoding)
    {
        $encodingMap = self::getAsciiEncodingMap();
        $generator   = $encodingMap[$fileEncoding];
        $encAttr     = $generator('encoding="');
        $quote       = $generator('"');
        $close       = $generator('>');

        $closePos    = strpos($xml, $close);
        if (false === $closePos) {
            return array($fileEncoding);
        }

        $encPos = strpos($xml, $encAttr);
        if (false === $encPos
            || $encPos > $closePos
        ) {
            return array($fileEncoding);
        }

        $encPos   += strlen($encAttr);
        $quotePos = strpos($xml, $quote, $encPos);
        if (false === $quotePos) {
            return array($fileEncoding);
        }

        $encoding = self::substr($xml, $encPos, $quotePos);
        return array(
            // Following line works because we're only supporting 8-bit safe encodings at this time.
            str_replace('\0', '', $encoding), // detected encoding
            $fileEncoding,                    // file encoding
        );
    }

    /**
     * Return a list of BOM maps.
     *
     * Returns a list of common encoding -> BOM maps, along with the character
     * length to compare against.
     *
     * @link https://en.wikipedia.org/wiki/Byte_order_mark
     * @return array
     */
    protected static function getBomMap()
    {
        return array(
            array(
                'encoding' => 'UTF-32BE',
                'bom'      => pack('CCCC', 0x00, 0x00, 0xfe, 0xff),
                'length'   => 4,
            ),
            array(
                'encoding' => 'UTF-32LE',
                'bom'      => pack('CCCC', 0xff, 0xfe, 0x00, 0x00),
                'length'   => 4,
            ),
            array(
                'encoding' => 'GB-18030',
                'bom'      => pack('CCCC', 0x84, 0x31, 0x95, 0x33),
                'length'   => 4,
            ),
            array(
                'encoding' => 'UTF-16BE',
                'bom'      => pack('CC', 0xfe, 0xff),
                'length'   => 2,
            ),
            array(
                'encoding' => 'UTF-16LE',
                'bom'      => pack('CC', 0xff, 0xfe),
                'length'   => 2,
            ),
            array(
                'encoding' => 'UTF-8',
                'bom'      => pack('CCC', 0xef, 0xbb, 0xbf),
                'length'   => 3,
            ),
        );
    }

    /**
     * Return a map of encoding => generator pairs.
     *
     * Returns a map of encoding => generator pairs, where the generator is a
     * callable that accepts a string and returns the appropriate byte order
     * sequence of that string for the encoding.
     *
     * @return array
     */
    protected static function getAsciiEncodingMap()
    {
        return array(
            'UTF-32BE'   => function ($ascii) {
                return preg_replace('/(.)/', "\0\0\0\\1", $ascii);
            },
            'UTF-32LE'   => function ($ascii) {
                return preg_replace('/(.)/', "\\1\0\0\0", $ascii);
            },
            'UTF-32odd1' => function ($ascii) {
                return preg_replace('/(.)/', "\0\\1\0\0", $ascii);
            },
            'UTF-32odd2' => function ($ascii) {
                return preg_replace('/(.)/', "\0\0\\1\0", $ascii);
            },
            'UTF-16BE'   => function ($ascii) {
                return preg_replace('/(.)/', "\0\\1", $ascii);
            },
            'UTF-16LE'   => function ($ascii) {
                return preg_replace('/(.)/', "\\1\0", $ascii);
            },
            'UTF-8'      => function ($ascii) {
                return $ascii;
            },
            'GB-18030'   => function ($ascii) {
                return $ascii;
            },
        );
    }

    /**
     * Binary-safe substr.
     *
     * substr() is not binary-safe; this method loops by character to ensure
     * multi-byte characters are aggregated correctly.
     *
     * @param string $string
     * @param int $start
     * @param int $end
     * @return string
     */
    protected static function substr($string, $start, $end)
    {
        $substr = '';
        for ($i = $start; $i < $end; $i += 1) {
            $substr .= $string[$i];
        }
        return $substr;
    }
}
