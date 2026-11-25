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

namespace PhpOffice\PhpWord\Reader\RTF;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;

/**
 * RTF document reader.
 *
 * References:
 * - How to Write an RTF Reader http://latex2rtf.sourceforge.net/rtfspec_45.html
 * - PHP rtfclass by Markus Fischer https://github.com/mfn/rtfclass
 * - JavaScript RTF-parser by LazyGyu https://github.com/lazygyu/RTF-parser
 *
 * @since 0.11.0
 *
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 */
class Document
{
    /** @const int */
    const PARA = 'readParagraph';
    const STYL = 'readStyle';
    const SKIP = 'readSkip';

    /**
     * PhpWord object.
     *
     * @var PhpWord
     */
    private $phpWord;

    /**
     * Section object.
     *
     * @var \PhpOffice\PhpWord\Element\Section
     */
    private $section;

    /**
     * Textrun object.
     *
     * @var \PhpOffice\PhpWord\Element\TextRun
     */
    private $textrun;

    /**
     * RTF content.
     *
     * @var string
     */
    public $rtf;

    /**
     * Content length.
     *
     * @var int
     */
    private $length = 0;

    /**
     * Character index.
     *
     * @var int
     */
    private $offset = 0;

    /**
     * Current control word.
     *
     * @var string
     */
    private $control = '';

    /**
     * Text content.
     *
     * @var string
     */
    private $text = '';

    /**
     * Parsing a control word flag.
     *
     * @var bool
     */
    private $isControl = false;

    /**
     * First character flag: watch out for control symbols.
     *
     * @var bool
     */
    private $isFirst = false;

    /**
     * Group groups.
     *
     * @var array
     */
    private $groups = [];

    /**
     * Parser flags; not used.
     *
     * @var array
     */
    private $flags = [];

    /**
     * Parse RTF content.
     *
     * - Marks controlling characters `{`, `}`, and `\`
     * - Removes line endings
     * - Builds control words and control symbols
     * - Pushes every other character into the text queue
     *
     * @todo Use `fread` stream for scalability
     */
    public function read(PhpWord $phpWord): void
    {
        $markers = [
            123 => 'markOpening',   // {
            125 => 'markClosing',   // }
            92 => 'markBackslash', // \
            10 => 'markNewline',   // LF
            13 => 'markNewline',   // CR
        ];

        $this->phpWord = $phpWord;
        $this->section = $phpWord->addSection();
        $this->textrun = $this->section->addTextRun();
        $this->length = strlen($this->rtf);

        $this->flags['paragraph'] = true; // Set paragraph flag from the beginning

        // Walk each characters
        while ($this->offset < $this->length) {
            $char = $this->rtf[$this->offset];
            $ascii = ord($char);

            if (isset($markers[$ascii])) { // Marker found: {, }, \, LF, or CR
                $markerFunction = $markers[$ascii];
                $this->$markerFunction();
            } else {
                if (false === $this->isControl) { // Non control word: Push character
                    $this->pushText($char);
                } else {
                    if (preg_match('/^[a-zA-Z0-9-]?$/', $char)) { // No delimiter: Buffer control
                        $this->control .= $char;
                        $this->isFirst = false;
                    } else { // Delimiter found: Parse buffered control
                        if ($this->isFirst) {
                            $this->isFirst = false;
                        } else {
                            if (' ' == $char) { // Discard space as a control word delimiter
                                $this->flushControl(true);
                            }
                        }
                    }
                }
            }
            ++$this->offset;
        }
        $this->flushText();
    }

    /**
     * Mark opening braket `{` character.
     */
    private function markOpening(): void
    {
        $this->flush(true);
        array_push($this->groups, $this->flags);
    }

    /**
     * Mark closing braket `}` character.
     */
    private function markClosing(): void
    {
        $this->flush(true);
        $this->flags = array_pop($this->groups);
    }

    /**
     * Mark backslash `\` character.
     */
    private function markBackslash(): void
    {
        if ($this->isFirst) {
            $this->setControl(false);
            $this->text .= '\\';
        } else {
            $this->flush();
            $this->setControl(true);
            $this->control = '';
        }
    }

    /**
     * Mark newline character: Flush control word because it's not possible to span multiline.
     */
    private function markNewline(): void
    {
        if ($this->isControl) {
            $this->flushControl(true);
        }
    }

    /**
     * Flush control word or text.
     *
     * @param bool $isControl
     */
    private function flush($isControl = false): void
    {
        if ($this->isControl) {
            $this->flushControl($isControl);
        } else {
            $this->flushText();
        }
    }

    /**
     * Flush control word.
     *
     * @param bool $isControl
     */
    private function flushControl($isControl = false): void
    {
        if (1 === preg_match('/^([A-Za-z]+)(-?[0-9]*) ?$/', $this->control, $match)) {
            [, $control, $parameter] = $match;
            $this->parseControl($control, $parameter);
        }

        if (true === $isControl) {
            $this->setControl(false);
        }
    }

    /**
     * Flush text in queue.
     */
    private function flushText(): void
    {
        if ($this->text != '') {
            if (isset($this->flags['property'])) { // Set property
                $this->flags['value'] = $this->text;
            } else { // Set text
                if (true === $this->flags['paragraph']) {
                    $this->flags['paragraph'] = false;
                    $this->flags['text'] = $this->text;
                }
            }

            // Add text if it's not flagged as skipped
            if (!isset($this->flags['skipped'])) {
                $this->readText();
            }

            $this->text = '';
        }
    }

    /**
     * Reset control word and first char state.
     *
     * @param bool $value
     */
    private function setControl($value): void
    {
        $this->isControl = $value;
        $this->isFirst = $value;
    }

    /**
     * Push text into queue.
     *
     * @param string $char
     */
    private function pushText($char): void
    {
        if ('<' == $char) {
            $this->text .= '&lt;';
        } elseif ('>' == $char) {
            $this->text .= '&gt;';
        } else {
            $this->text .= $char;
        }
    }

    /**
     * Parse control.
     *
     * @param string $control
     * @param string $parameter
     */
    private function parseControl($control, $parameter): void
    {
        $controls = [
            'par' => [self::PARA,    'paragraph',    true],
            'b' => [self::STYL,    'font',         'bold',          true],
            'i' => [self::STYL,    'font',         'italic',        true],
            'u' => [self::STYL,    'font',         'underline',     true],
            'strike' => [self::STYL,    'font',         'strikethrough', true],
            'fs' => [self::STYL,    'font',         'size',          $parameter],
            'qc' => [self::STYL,    'paragraph',    'alignment',     Jc::CENTER],
            'sa' => [self::STYL,    'paragraph',    'spaceAfter',    $parameter],
            'fonttbl' => [self::SKIP,    'fonttbl',      null],
            'colortbl' => [self::SKIP,    'colortbl',     null],
            'info' => [self::SKIP,    'info',         null],
            'generator' => [self::SKIP,    'generator',    null],
            'title' => [self::SKIP,    'title',        null],
            'subject' => [self::SKIP,    'subject',      null],
            'category' => [self::SKIP,    'category',     null],
            'keywords' => [self::SKIP,    'keywords',     null],
            'comment' => [self::SKIP,    'comment',      null],
            'shppict' => [self::SKIP,    'pic',          null],
            'fldinst' => [self::SKIP,    'link',         null],
        ];

        if (isset($controls[$control])) {
            [$function] = $controls[$control];
            if (method_exists($this, $function)) {
                $directives = $controls[$control];
                array_shift($directives); // remove the function variable; we won't need it
                $this->$function($directives);
            }
        }
    }

    /**
     * Read paragraph.
     *
     * @param array $directives
     */
    private function readParagraph($directives): void
    {
        [$property, $value] = $directives;
        $this->textrun = $this->section->addTextRun();
        $this->flags[$property] = $value;
    }

    /**
     * Read style.
     *
     * @param array $directives
     */
    private function readStyle($directives): void
    {
        [$style, $property, $value] = $directives;
        $this->flags['styles'][$style][$property] = $value;
    }

    /**
     * Read skip.
     *
     * @param array $directives
     */
    private function readSkip($directives): void
    {
        [$property] = $directives;
        $this->flags['property'] = $property;
        $this->flags['skipped'] = true;
    }

    /**
     * Read text.
     */
    private function readText(): void
    {
        $text = $this->textrun->addText($this->text);
        if (isset($this->flags['styles']['font'])) {
            $text->getFontStyle()->setStyleByArray($this->flags['styles']['font']);
        }
    }
}
