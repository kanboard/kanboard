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

namespace PhpOffice\PhpWord\Shared;

use DOMDocument;
use DOMElement;
use DOMNodeList;
use DOMXpath;
use Exception;
use InvalidArgumentException;
use ZipArchive;

/**
 * XML Reader wrapper.
 *
 * @since   0.2.1
 */
class XMLReader
{
    /**
     * DOMDocument object.
     *
     * @var DOMDocument
     */
    private $dom;

    /**
     * DOMXpath object.
     *
     * @var DOMXpath
     */
    private $xpath;

    /**
     * Get DOMDocument from ZipArchive.
     *
     * @param string $zipFile
     * @param string $xmlFile
     *
     * @return DOMDocument|false
     */
    public function getDomFromZip($zipFile, $xmlFile)
    {
        if (file_exists($zipFile) === false) {
            throw new Exception('Cannot find archive file.');
        }

        $zip = new ZipArchive();
        $openStatus = $zip->open($zipFile);
        if ($openStatus !== true) {
            /**
             * Throw an exception since making further calls on the ZipArchive would cause a fatal error.
             * This prevents fatal errors on corrupt archives and attempts to open old "doc" files.
             */
            throw new Exception("The archive failed to load with the following error code: $openStatus");
        }

        $content = $zip->getFromName(ltrim($xmlFile, '/'));
        $zip->close();

        if ($content === false) {
            return false;
        }

        return $this->getDomFromString($content);
    }

    /**
     * Get DOMDocument from content string.
     *
     * @param string $content
     *
     * @return DOMDocument
     */
    public function getDomFromString($content)
    {
        if (\PHP_VERSION_ID < 80000) {
            $originalLibXMLEntityValue = libxml_disable_entity_loader(true);
        }
        $this->dom = new DOMDocument();
        $this->dom->loadXML($content);
        if (\PHP_VERSION_ID < 80000) {
            libxml_disable_entity_loader($originalLibXMLEntityValue);
        }

        return $this->dom;
    }

    /**
     * Get elements.
     *
     * @param string $path
     *
     * @return DOMNodeList<DOMElement>
     */
    public function getElements($path, ?DOMElement $contextNode = null)
    {
        if ($this->dom === null) {
            return new DOMNodeList(); // @phpstan-ignore-line
        }
        if ($this->xpath === null) {
            $this->xpath = new DOMXpath($this->dom);
        }

        $result = @$this->xpath->query($path, $contextNode);

        return empty($result) ? new DOMNodeList() : $result; // @phpstan-ignore-line
    }

    /**
     * Registers the namespace with the DOMXPath object.
     *
     * @param string $prefix The prefix
     * @param string $namespaceURI The URI of the namespace
     *
     * @return bool true on success or false on failure
     */
    public function registerNamespace($prefix, $namespaceURI)
    {
        if ($this->dom === null) {
            throw new InvalidArgumentException('Dom needs to be loaded before registering a namespace');
        }
        if ($this->xpath === null) {
            $this->xpath = new DOMXpath($this->dom);
        }

        return $this->xpath->registerNamespace($prefix, $namespaceURI);
    }

    /**
     * Get element.
     *
     * @param string $path
     *
     * @return null|DOMElement
     */
    public function getElement($path, ?DOMElement $contextNode = null)
    {
        $elements = $this->getElements($path, $contextNode);
        if ($elements->length > 0) {
            return $elements->item(0);
        }

        return null;
    }

    /**
     * Get element attribute.
     *
     * @param string $attribute
     * @param string $path
     *
     * @return null|string
     */
    public function getAttribute($attribute, ?DOMElement $contextNode = null, $path = null)
    {
        $return = null;
        if ($path !== null) {
            $elements = $this->getElements($path, $contextNode);
            if ($elements->length > 0) {
                /** @var DOMElement $node Type hint */
                $node = $elements->item(0);
                $return = $node->getAttribute($attribute);
            }
        } else {
            if ($contextNode !== null) {
                $return = $contextNode->getAttribute($attribute);
            }
        }

        return ($return == '') ? null : $return;
    }

    /**
     * Get element value.
     *
     * @param string $path
     *
     * @return null|string
     */
    public function getValue($path, ?DOMElement $contextNode = null)
    {
        $elements = $this->getElements($path, $contextNode);
        if ($elements->length > 0) {
            return $elements->item(0)->nodeValue;
        }

        return null;
    }

    /**
     * Count elements.
     *
     * @param string $path
     *
     * @return int
     */
    public function countElements($path, ?DOMElement $contextNode = null)
    {
        $elements = $this->getElements($path, $contextNode);

        return $elements->length;
    }

    /**
     * Element exists.
     *
     * @param string $path
     *
     * @return bool
     */
    public function elementExists($path, ?DOMElement $contextNode = null)
    {
        return $this->getElements($path, $contextNode)->length > 0;
    }
}
