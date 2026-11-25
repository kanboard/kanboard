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

namespace PhpOffice\PhpWord;

use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\Reader\ReaderInterface;
use PhpOffice\PhpWord\Writer\WriterInterface;
use ReflectionClass;

abstract class IOFactory
{
    /**
     * Create new writer.
     *
     * @param string $name
     *
     * @return WriterInterface
     */
    public static function createWriter(PhpWord $phpWord, $name = 'Word2007')
    {
        if ($name !== 'WriterInterface' && !in_array($name, ['ODText', 'RTF', 'Word2007', 'HTML', 'PDF', 'EPub3'], true)) {
            throw new Exception("\"{$name}\" is not a valid writer.");
        }

        $fqName = "PhpOffice\\PhpWord\\Writer\\{$name}";

        return new $fqName($phpWord);
    }

    /**
     * Create new reader.
     *
     * @param string $name
     *
     * @return ReaderInterface
     */
    public static function createReader($name = 'Word2007')
    {
        return self::createObject('Reader', $name);
    }

    /**
     * Create new object.
     *
     * @param string $type
     * @param string $name
     * @param PhpWord $phpWord
     *
     * @return ReaderInterface|WriterInterface
     */
    private static function createObject($type, $name, $phpWord = null)
    {
        $class = "PhpOffice\\PhpWord\\{$type}\\{$name}";
        if (class_exists($class) && self::isConcreteClass($class)) {
            return new $class($phpWord);
        }

        throw new Exception("\"{$name}\" is not a valid {$type}.");
    }

    /**
     * Loads PhpWord from file.
     *
     * @param string $filename The name of the file
     * @param string $readerName
     *
     * @return PhpWord $phpWord
     */
    public static function load($filename, $readerName = 'Word2007')
    {
        /** @var ReaderInterface $reader */
        $reader = self::createReader($readerName);

        return $reader->load($filename);
    }

    /**
     * Loads PhpWord ${variable} from file.
     *
     * @param string $filename The name of the file
     *
     * @return array The extracted variables
     */
    public static function extractVariables(string $filename, string $readerName = 'Word2007'): array
    {
        /** @var ReaderInterface $reader */
        $reader = self::createReader($readerName);
        $document = $reader->load($filename);
        $extractedVariables = [];
        foreach ($document->getSections() as $section) {
            $concatenatedText = '';
            foreach ($section->getElements() as $element) {
                if ($element instanceof TextRun) {
                    foreach ($element->getElements() as $textElement) {
                        if ($textElement instanceof Text) {
                            $text = $textElement->getText();
                            $concatenatedText .= $text;
                        }
                    }
                }
            }
            preg_match_all('/\$\{([^}]+)\}/', $concatenatedText, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $match) {
                    $trimmedMatch = trim($match);
                    $extractedVariables[] = $trimmedMatch;
                }
            }
        }

        return $extractedVariables;
    }

    /**
     * Check if it's a concrete class (not abstract nor interface).
     *
     * @param string $class
     *
     * @return bool
     */
    private static function isConcreteClass($class)
    {
        $reflection = new ReflectionClass($class);

        return !$reflection->isAbstract() && !$reflection->isInterface();
    }
}
