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
 * @copyright   2010-2018 PHPWord contributors
 * @license     http://www.gnu.org/licenses/lgpl.txt LGPL version 3
 */

namespace PhpOffice\PhpWord\Shared;

use PhpOffice\PhpWord\Exception\Exception;

defined('IDENTIFIER_OLE') ||
define('IDENTIFIER_OLE', pack('CCCCCCCC', 0xd0, 0xcf, 0x11, 0xe0, 0xa1, 0xb1, 0x1a, 0xe1));

class OLERead
{
    private $data = '';

    // OLE identifier
    const IDENTIFIER_OLE = IDENTIFIER_OLE;

    // Size of a sector = 512 bytes
    const BIG_BLOCK_SIZE = 0x200;

    // Size of a short sector = 64 bytes
    const SMALL_BLOCK_SIZE = 0x40;

    // Size of a directory entry always = 128 bytes
    const PROPERTY_STORAGE_BLOCK_SIZE = 0x80;

    // Minimum size of a standard stream = 4096 bytes, streams smaller than this are stored as short streams
    const SMALL_BLOCK_THRESHOLD = 0x1000;

    // header offsets
    const NUM_BIG_BLOCK_DEPOT_BLOCKS_POS = 0x2c;
    const ROOT_START_BLOCK_POS = 0x30;
    const SMALL_BLOCK_DEPOT_BLOCK_POS = 0x3c;
    const EXTENSION_BLOCK_POS = 0x44;
    const NUM_EXTENSION_BLOCK_POS = 0x48;
    const BIG_BLOCK_DEPOT_BLOCKS_POS = 0x4c;

    // property storage offsets (directory offsets)
    const SIZE_OF_NAME_POS = 0x40;
    const TYPE_POS = 0x42;
    const START_BLOCK_POS = 0x74;
    const SIZE_POS = 0x78;

    public $wrkdocument = null;
    public $wrk1Table = null;
    public $wrkData = null;
    public $wrkObjectPool = null;
    public $summaryInformation = null;
    public $docSummaryInfos = null;
    public $numBigBlockDepotBlocks = null;
    public $rootStartBlock = null;
    public $sbdStartBlock = null;
    public $extensionBlock = null;
    public $numExtensionBlocks = null;
    public $bigBlockChain = null;
    public $smallBlockChain = null;	
    public $entry = null;	
    public $rootentry = null;
    public $wrkObjectPoolelseif = null;
    public $props = array();	

    /**
     * Read the file
     *
     * @param $sFileName string Filename
     *
     * @throws Exception
     */
    public function read($sFileName)
    {
        // Check if file exists and is readable
        if (!is_readable($sFileName)) {
            throw new Exception('Could not open ' . $sFileName . ' for reading! File does not exist, or it is not readable.');
        }

        // Get the file identifier
        // Don't bother reading the whole file until we know it's a valid OLE file
        $this->data = file_get_contents($sFileName, false, null, 0, 8);

        // Check OLE identifier
        if ($this->data != self::IDENTIFIER_OLE) {
            throw new Exception('The filename ' . $sFileName . ' is not recognised as an OLE file');
        }

        // Get the file data
        $this->data = file_get_contents($sFileName);

        // Total number of sectors used for the SAT
        $this->numBigBlockDepotBlocks = self::getInt4d($this->data, self::NUM_BIG_BLOCK_DEPOT_BLOCKS_POS);

        // SecID of the first sector of the directory stream
        $this->rootStartBlock = self::getInt4d($this->data, self::ROOT_START_BLOCK_POS);

        // SecID of the first sector of the SSAT (or -2 if not extant)
        $this->sbdStartBlock = self::getInt4d($this->data, self::SMALL_BLOCK_DEPOT_BLOCK_POS);

        // SecID of the first sector of the MSAT (or -2 if no additional sectors are used)
        $this->extensionBlock = self::getInt4d($this->data, self::EXTENSION_BLOCK_POS);

        // Total number of sectors used by MSAT
        $this->numExtensionBlocks = self::getInt4d($this->data, self::NUM_EXTENSION_BLOCK_POS);

        $bigBlockDepotBlocks = array();
        $pos = self::BIG_BLOCK_DEPOT_BLOCKS_POS;

        $bbdBlocks = $this->numBigBlockDepotBlocks;

        // @codeCoverageIgnoreStart
        if ($this->numExtensionBlocks != 0) {
            $bbdBlocks = (self::BIG_BLOCK_SIZE - self::BIG_BLOCK_DEPOT_BLOCKS_POS) / 4;
        }
        // @codeCoverageIgnoreEnd

        for ($i = 0; $i < $bbdBlocks; ++$i) {
            $bigBlockDepotBlocks[$i] = self::getInt4d($this->data, $pos);
            $pos += 4;
        }

        // @codeCoverageIgnoreStart
        for ($j = 0; $j < $this->numExtensionBlocks; ++$j) {
            $pos = ($this->extensionBlock + 1) * self::BIG_BLOCK_SIZE;
            $blocksToRead = min($this->numBigBlockDepotBlocks - $bbdBlocks, self::BIG_BLOCK_SIZE / 4 - 1);

            for ($i = $bbdBlocks; $i < $bbdBlocks + $blocksToRead; ++$i) {
                $bigBlockDepotBlocks[$i] = self::getInt4d($this->data, $pos);
                $pos += 4;
            }

            $bbdBlocks += $blocksToRead;
            if ($bbdBlocks < $this->numBigBlockDepotBlocks) {
                $this->extensionBlock = self::getInt4d($this->data, $pos);
            }
        }
        // @codeCoverageIgnoreEnd

        $pos = 0;
        $this->bigBlockChain = '';
        $bbs = self::BIG_BLOCK_SIZE / 4;
        for ($i = 0; $i < $this->numBigBlockDepotBlocks; ++$i) {
            $pos = ($bigBlockDepotBlocks[$i] + 1) * self::BIG_BLOCK_SIZE;

            $this->bigBlockChain .= substr($this->data, $pos, 4 * $bbs);
            $pos += 4 * $bbs;
        }

        $pos = 0;
        $sbdBlock = $this->sbdStartBlock;
        $this->smallBlockChain = '';
        while ($sbdBlock != -2) {
            $pos = ($sbdBlock + 1) * self::BIG_BLOCK_SIZE;

            $this->smallBlockChain .= substr($this->data, $pos, 4 * $bbs);
            $pos += 4 * $bbs;

            $sbdBlock = self::getInt4d($this->bigBlockChain, $sbdBlock * 4);
        }

        // read the directory stream
        $block = $this->rootStartBlock;
        $this->entry = $this->readData($block);

        $this->readPropertySets();
    }

    /**
     * Extract binary stream data
     *
     * @param mixed $stream
     * @return string
     */
    public function getStream($stream)
    {
        if ($stream === null) {
            return null;
        }

        $streamData = '';

        if ($this->props[$stream]['size'] < self::SMALL_BLOCK_THRESHOLD) {
            $rootdata = $this->readData($this->props[$this->rootentry]['startBlock']);

            $block = $this->props[$stream]['startBlock'];

            while ($block != -2) {
                $pos = $block * self::SMALL_BLOCK_SIZE;
                $streamData .= substr($rootdata, $pos, self::SMALL_BLOCK_SIZE);

                $block = self::getInt4d($this->smallBlockChain, $block * 4);
            }

            return $streamData;
        }

        $numBlocks = $this->props[$stream]['size'] / self::BIG_BLOCK_SIZE;
        if ($this->props[$stream]['size'] % self::BIG_BLOCK_SIZE != 0) {
            ++$numBlocks;
        }

        if ($numBlocks == 0) {
            return ''; // @codeCoverageIgnore
        }

        $block = $this->props[$stream]['startBlock'];

        while ($block != -2) {
            $pos = ($block + 1) * self::BIG_BLOCK_SIZE;
            $streamData .= substr($this->data, $pos, self::BIG_BLOCK_SIZE);
            $block = self::getInt4d($this->bigBlockChain, $block * 4);
        }

        return $streamData;
    }

    /**
     * Read a standard stream (by joining sectors using information from SAT)
     *
     * @param int $blSectorId Sector ID where the stream starts
     * @return string Data for standard stream
     */
    private function readData($blSectorId)
    {
        $block = $blSectorId;
        $data = '';

        while ($block != -2) {
            $pos = ($block + 1) * self::BIG_BLOCK_SIZE;
            $data .= substr($this->data, $pos, self::BIG_BLOCK_SIZE);
            $block = self::getInt4d($this->bigBlockChain, $block * 4);
        }

        return $data;
    }

    /**
     * Read entries in the directory stream.
     */
    private function readPropertySets()
    {
        $offset = 0;

        // loop through entires, each entry is 128 bytes
        $entryLen = strlen($this->entry);
        while ($offset < $entryLen) {
            // entry data (128 bytes)
            $data = substr($this->entry, $offset, self::PROPERTY_STORAGE_BLOCK_SIZE);

            // size in bytes of name
            $nameSize = ord($data[self::SIZE_OF_NAME_POS]) | (ord($data[self::SIZE_OF_NAME_POS + 1]) << 8);

            // type of entry
            $type = ord($data[self::TYPE_POS]);

            // sectorID of first sector or short sector, if this entry refers to a stream (the case with workbook)
            // sectorID of first sector of the short-stream container stream, if this entry is root entry
            $startBlock = self::getInt4d($data, self::START_BLOCK_POS);

            $size = self::getInt4d($data, self::SIZE_POS);

            $name = str_replace("\x00", '', substr($data, 0, $nameSize));

            $this->props[] = array(
                'name'       => $name,
                'type'       => $type,
                'startBlock' => $startBlock,
                'size'       => $size, );

            // tmp helper to simplify checks
            $upName = strtoupper($name);

            // Workbook directory entry (BIFF5 uses Book, BIFF8 uses Workbook)
            // print_r($upName.PHP_EOL);
            if (($upName === 'WORDDOCUMENT')) {
                $this->wrkdocument = count($this->props) - 1;
            } elseif ($upName === '1TABLE') {
                $this->wrk1Table = count($this->props) - 1;
            } elseif ($upName === 'DATA') {
                $this->wrkData = count($this->props) - 1;
            } elseif ($upName === 'OBJECTPOOL') {
                $this->wrkObjectPoolelseif = count($this->props) - 1;
            } elseif ($upName === 'ROOT ENTRY' || $upName === 'R') {
                $this->rootentry = count($this->props) - 1;
            }

            // Summary information
            if ($name == chr(5) . 'SummaryInformation') {
                $this->summaryInformation = count($this->props) - 1;
            }

            // Additional Document Summary information
            if ($name == chr(5) . 'DocumentSummaryInformation') {
                $this->docSummaryInfos = count($this->props) - 1;
            }

            $offset += self::PROPERTY_STORAGE_BLOCK_SIZE;
        }
    }

    /**
     * Read 4 bytes of data at specified position
     *
     * @param string $data
     * @param int $pos
     * @return int
     */
    private static function getInt4d($data, $pos)
    {
        // FIX: represent numbers correctly on 64-bit system
        // http://sourceforge.net/tracker/index.php?func=detail&aid=1487372&group_id=99160&atid=623334
        // Hacked by Andreas Rehm 2006 to ensure correct result of the <<24 block on 32 and 64bit systems
        $or24 = ord($data[$pos + 3]);
        if ($or24 >= 128) {
            // negative number
            $ord24 = -abs((256 - $or24) << 24);
        } else {
            $ord24 = ($or24 & 127) << 24;
        }

        return ord($data[$pos]) | (ord($data[$pos + 1]) << 8) | (ord($data[$pos + 2]) << 16) | $ord24;
    }
}
