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

namespace PhpOffice\PhpWord\Reader;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Drawing;
use PhpOffice\PhpWord\Shared\OLERead;
use PhpOffice\PhpWord\Style;
use stdClass;

/**
 * Reader for Word97.
 *
 * @since 0.10.0
 */
class MsDoc extends AbstractReader implements ReaderInterface
{
    /**
     * PhpWord object.
     *
     * @var PhpWord
     */
    private $phpWord;

    /**
     * WordDocument Stream.
     */
    private $dataWorkDocument;

    /**
     * 1Table Stream.
     */
    private $data1Table;

    /**
     * Data Stream.
     */
    private $dataData;

    /**
     * Object Pool Stream.
     */
    private $dataObjectPool;

    /**
     * @var stdClass[]
     */
    private $arrayCharacters = [];

    /**
     * @var array
     */
    private $arrayFib = [];

    /**
     * @var string[]
     */
    private $arrayFonts = [];

    /**
     * @var string[]
     */
    private $arrayParagraphs = [];

    /**
     * @var stdClass[]
     */
    private $arraySections = [];

    /** @var string */
    private $summaryInformation;

    /** @var string */
    private $documentSummaryInformation;

    const VERSION_97 = '97';
    const VERSION_2000 = '2000';
    const VERSION_2002 = '2002';
    const VERSION_2003 = '2003';
    const VERSION_2007 = '2007';

    const SPRA_VALUE = 10;
    const SPRA_VALUE_OPPOSITE = 20;

    const OFFICEARTBLIPEMF = 0xF01A;
    const OFFICEARTBLIPWMF = 0xF01B;
    const OFFICEARTBLIPPICT = 0xF01C;
    const OFFICEARTBLIPJPG = 0xF01D;
    const OFFICEARTBLIPPNG = 0xF01E;
    const OFFICEARTBLIPDIB = 0xF01F;
    const OFFICEARTBLIPTIFF = 0xF029;
    const OFFICEARTBLIPJPEG = 0xF02A;

    const MSOBLIPERROR = 0x00;
    const MSOBLIPUNKNOWN = 0x01;
    const MSOBLIPEMF = 0x02;
    const MSOBLIPWMF = 0x03;
    const MSOBLIPPICT = 0x04;
    const MSOBLIPJPEG = 0x05;
    const MSOBLIPPNG = 0x06;
    const MSOBLIPDIB = 0x07;
    const MSOBLIPTIFF = 0x11;
    const MSOBLIPCMYKJPEG = 0x12;

    /**
     * Loads PhpWord from file.
     *
     * @param string $filename
     *
     * @return PhpWord
     */
    public function load($filename)
    {
        $this->phpWord = new PhpWord();

        $this->loadOLE($filename);

        $this->readFib($this->dataWorkDocument);
        $this->readFibContent();

        return $this->phpWord;
    }

    /**
     * Load an OLE Document.
     *
     * @param string $filename
     */
    private function loadOLE($filename): void
    {
        // OLE reader
        $ole = new OLERead();
        $ole->read($filename);

        // Get WorkDocument stream
        $this->dataWorkDocument = $ole->getStream($ole->wrkdocument);
        // Get 1Table stream
        $this->data1Table = $ole->getStream($ole->wrk1Table);
        // Get Data stream
        $this->dataData = $ole->getStream($ole->wrkData);
        // Get Data stream
        $this->dataObjectPool = $ole->getStream($ole->wrkObjectPool);
        // Get Summary Information data
        $this->summaryInformation = $ole->getStream($ole->summaryInformation);
        // Get Document Summary Information data
        $this->documentSummaryInformation = $ole->getStream($ole->docSummaryInfos);
    }

    private function getNumInLcb($lcb, $iSize)
    {
        return ($lcb - 4) / (4 + $iSize);
    }

    private function getArrayCP($data, $posMem, $iNum)
    {
        $arrayCP = [];
        for ($inc = 0; $inc < $iNum; ++$inc) {
            $arrayCP[$inc] = self::getInt4d($data, $posMem);
            $posMem += 4;
        }

        return $arrayCP;
    }

    /**
     * @see  http://msdn.microsoft.com/en-us/library/dd949344%28v=office.12%29.aspx
     * @see  https://igor.io/2012/09/24/binary-parsing.html
     *
     * @param string $data
     */
    private function readFib($data)
    {
        $pos = 0;
        //----- FibBase
        // wIdent
        $pos += 2;
        // nFib
        $pos += 2;
        // unused
        $pos += 2;
        // lid : Language Identifier
        $pos += 2;
        // pnNext
        $pos += 2;

        // $mem = self::getInt2d($data, $pos);
        // $fDot = ($mem >> 15) & 1;
        // $fGlsy = ($mem >> 14) & 1;
        // $fComplex = ($mem >> 13) & 1;
        // $fHasPic = ($mem >> 12) & 1;
        // $cQuickSaves = ($mem >> 8) & bindec('1111');
        // $fEncrypted = ($mem >> 7) & 1;
        // $fWhichTblStm = ($mem >> 6) & 1;
        // $fReadOnlyRecommended = ($mem >> 5) & 1;
        // $fWriteReservation = ($mem >> 4) & 1;
        // $fExtChar = ($mem >> 3) & 1;
        // $fLoadOverride = ($mem >> 2) & 1;
        // $fFarEast = ($mem >> 1) & 1;
        // $fObfuscated = ($mem >> 0) & 1;
        $pos += 2;
        // nFibBack
        $pos += 2;
        // lKey
        $pos += 4;
        // envr
        ++$pos;

        // $mem = self::getInt1d($data, $pos);
        // $fMac = ($mem >> 7) & 1;
        // $fEmptySpecial = ($mem >> 6) & 1;
        // $fLoadOverridePage = ($mem >> 5) & 1;
        // $reserved1 = ($mem >> 4) & 1;
        // $reserved2 = ($mem >> 3) & 1;
        // $fSpare0 = ($mem >> 0) & bindec('111');
        ++$pos;

        // reserved3
        $pos += 2;
        // reserved4
        $pos += 2;
        // reserved5
        $pos += 4;
        // reserved6
        $pos += 4;

        //----- csw
        $pos += 2;

        //----- fibRgW
        // reserved1
        $pos += 2;
        // reserved2
        $pos += 2;
        // reserved3
        $pos += 2;
        // reserved4
        $pos += 2;
        // reserved5
        $pos += 2;
        // reserved6
        $pos += 2;
        // reserved7
        $pos += 2;
        // reserved8
        $pos += 2;
        // reserved9
        $pos += 2;
        // reserved10
        $pos += 2;
        // reserved11
        $pos += 2;
        // reserved12
        $pos += 2;
        // reserved13
        $pos += 2;
        // lidFE
        $pos += 2;

        //----- cslw
        $pos += 2;

        //----- fibRgLw
        // cbMac
        $pos += 4;
        // reserved1
        $pos += 4;
        // reserved2
        $pos += 4;
        $this->arrayFib['ccpText'] = self::getInt4d($data, $pos);
        $pos += 4;
        $this->arrayFib['ccpFtn'] = self::getInt4d($data, $pos);
        $pos += 4;
        $this->arrayFib['ccpHdd'] = self::getInt4d($data, $pos);
        $pos += 4;
        // reserved3
        $pos += 4;
        // ccpAtn
        $pos += 4;
        // ccpEdn
        $pos += 4;
        // ccpTxbx
        $pos += 4;
        // ccpHdrTxbx
        $pos += 4;
        // reserved4
        $pos += 4;
        // reserved5
        $pos += 4;
        // reserved6
        $pos += 4;
        // reserved7
        $pos += 4;
        // reserved8
        $pos += 4;
        // reserved9
        $pos += 4;
        // reserved10
        $pos += 4;
        // reserved11
        $pos += 4;
        // reserved12
        $pos += 4;
        // reserved13
        $pos += 4;
        // reserved14
        $pos += 4;

        //----- cbRgFcLcb
        $cbRgFcLcb = self::getInt2d($data, $pos);
        $pos += 2;
        //----- fibRgFcLcbBlob
        switch ($cbRgFcLcb) {
            case 0x005D:
                $pos = $this->readBlockFibRgFcLcb($data, $pos, self::VERSION_97);

                break;
            case 0x006C:
                $pos = $this->readBlockFibRgFcLcb($data, $pos, self::VERSION_97);
                $pos = $this->readBlockFibRgFcLcb($data, $pos, self::VERSION_2000);

                break;
            case 0x0088:
                $pos = $this->readBlockFibRgFcLcb($data, $pos, self::VERSION_97);
                $pos = $this->readBlockFibRgFcLcb($data, $pos, self::VERSION_2000);
                $pos = $this->readBlockFibRgFcLcb($data, $pos, self::VERSION_2002);

                break;
            case 0x00A4:
                $pos = $this->readBlockFibRgFcLcb($data, $pos, self::VERSION_97);
                $pos = $this->readBlockFibRgFcLcb($data, $pos, self::VERSION_2000);
                $pos = $this->readBlockFibRgFcLcb($data, $pos, self::VERSION_2002);
                $pos = $this->readBlockFibRgFcLcb($data, $pos, self::VERSION_2003);

                break;
            case 0x00B7:
                $pos = $this->readBlockFibRgFcLcb($data, $pos, self::VERSION_97);
                $pos = $this->readBlockFibRgFcLcb($data, $pos, self::VERSION_2000);
                $pos = $this->readBlockFibRgFcLcb($data, $pos, self::VERSION_2002);
                $pos = $this->readBlockFibRgFcLcb($data, $pos, self::VERSION_2003);
                $pos = $this->readBlockFibRgFcLcb($data, $pos, self::VERSION_2007);

                break;
        }
        //----- cswNew
        $this->arrayFib['cswNew'] = self::getInt2d($data, $pos);
        $pos += 2;

        if ($this->arrayFib['cswNew'] != 0) {
            //@todo : fibRgCswNew
        }

        return $pos;
    }

    private function readBlockFibRgFcLcb($data, $pos, $version)
    {
        if ($version == self::VERSION_97) {
            $this->arrayFib['fcStshfOrig'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbStshfOrig'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcStshf'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbStshf'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcffndRef'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcffndRef'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcffndTxt'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcffndTxt'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfandRef'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfandRef'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfandTxt'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfandTxt '] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfSed'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfSed'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcPad'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcPad'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfPhe'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfPhe'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcSttbfGlsy'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbSttbfGlsy'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfGlsy'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfGlsy'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfHdd'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfHdd'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfBteChpx'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfBteChpx'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfBtePapx'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfBtePapx'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfSea'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfSea'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcSttbfFfn'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbSttbfFfn'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfFldMom'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfFldMom'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfFldHdr'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfFldHdr'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfFldFtn'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfFldFtn'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfFldAtn'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfFldAtn'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfFldMcr'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfFldMcr'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcSttbfBkmk'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbSttbfBkmk'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfBkf'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfBkf'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfBkl'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfBkl'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcCmds'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbCmds'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcUnused1'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbUnused1'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcSttbfMcr'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbSttbfMcr'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPrDrvr'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPrDrvr'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPrEnvPort'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPrEnvPort'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPrEnvLand'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPrEnvLand'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcWss'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbWss'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcDop'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbDop'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcSttbfAssoc'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbSttbfAssoc'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcClx'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbClx'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfPgdFtn'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfPgdFtn'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcAutosaveSource'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbAutosaveSource'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcGrpXstAtnOwners'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbGrpXstAtnOwners'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcSttbfAtnBkmk'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbSttbfAtnBkmk'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcUnused2'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbUnused2'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcUnused3'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbUnused3'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcSpaMom'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcSpaMom'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcSpaHdr'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcSpaHdr'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfAtnBkf'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfAtnBkf'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfAtnBkl'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfAtnBkl'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPms'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPms'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcFormFldSttbs'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbFormFldSttbs'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfendRef'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfendRef'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfendTxt'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfendTxt'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfFldEdn'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfFldEdn'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcUnused4'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbUnused4'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcDggInfo'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbDggInfo'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcSttbfRMark'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbSttbfRMark'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcSttbfCaption'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbSttbfCaption'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcSttbfAutoCaption'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbSttbfAutoCaption'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfWkb'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfWkb'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfSpl'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfSpl'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcftxbxTxt'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcftxbxTxt'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfFldTxbx'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfFldTxbx'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfHdrtxbxTxt'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfHdrtxbxTxt'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcffldHdrTxbx'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcffldHdrTxbx'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcStwUser'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbStwUser'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcSttbTtmbd'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbSttbTtmbd'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcCookieData'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbCookieData'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPgdMotherOldOld'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPgdMotherOldOld'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcBkdMotherOldOld'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbBkdMotherOldOld'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPgdFtnOldOld'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPgdFtnOldOld'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcBkdFtnOldOld'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbBkdFtnOldOld'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPgdEdnOldOld'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPgdEdnOldOld'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcBkdEdnOldOld'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbBkdEdnOldOld'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcSttbfIntlFld'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbSttbfIntlFld'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcRouteSlip'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbRouteSlip'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcSttbSavedBy'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbSttbSavedBy'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcSttbFnm'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbSttbFnm'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlfLst'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlfLst'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlfLfo'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlfLfo'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfTxbxBkd'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfTxbxBkd'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfTxbxHdrBkd'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfTxbxHdrBkd'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcDocUndoWord9'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbDocUndoWord9'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcRgbUse'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbRgbUse'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcUsp'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbUsp'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcUskf'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbUskf'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcupcRgbUse'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcupcRgbUse'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcupcUsp'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcupcUsp'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcSttbGlsyStyle'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbSttbGlsyStyle'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlgosl'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlgosl'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcocx'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcocx'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfBteLvc'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfBteLvc'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['dwLowDateTime'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['dwHighDateTime'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfLvcPre10'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfLvcPre10'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfAsumy'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfAsumy'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfGram'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfGram'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcSttbListNames'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbSttbListNames'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcSttbfUssr'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbSttbfUssr'] = self::getInt4d($data, $pos);
            $pos += 4;
        }
        if ($version == self::VERSION_2000) {
            $this->arrayFib['fcPlcfTch'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfTch'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcRmdThreading'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbRmdThreading'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcMid'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbMid'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcSttbRgtplc'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbSttbRgtplc'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcMsoEnvelope'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbMsoEnvelope'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfLad'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfLad'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcRgDofr'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbRgDofr'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcosl'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcosl'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfCookieOld'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfCookieOld'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPgdMotherOld'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPgdMotherOld'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcBkdMotherOld'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbBkdMotherOld'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPgdFtnOld'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPgdFtnOld'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcBkdFtnOld'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbBkdFtnOld'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPgdEdnOld'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPgdEdnOld'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcBkdEdnOld'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbBkdEdnOld'] = self::getInt4d($data, $pos);
            $pos += 4;
        }
        if ($version == self::VERSION_2002) {
            $this->arrayFib['fcUnused1'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbUnused1'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfPgp'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfPgp'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfuim'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfuim'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlfguidUim'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlfguidUim'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcAtrdExtra'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbAtrdExtra'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlrsid'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlrsid'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcSttbfBkmkFactoid'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbSttbfBkmkFactoid'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfBkfFactoid'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfBkfFactoid'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfcookie'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfcookie'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfBklFactoid'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfBklFactoid'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcFactoidData'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbFactoidData'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcDocUndo'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbDocUndo'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcSttbfBkmkFcc'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbSttbfBkmkFcc'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfBkfFcc'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfBkfFcc'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfBklFcc'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfBklFcc'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcSttbfbkmkBPRepairs'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbSttbfbkmkBPRepairs'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfbkfBPRepairs'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfbkfBPRepairs'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfbklBPRepairs'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfbklBPRepairs'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPmsNew'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPmsNew'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcODSO'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbODSO'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfpmiOldXP'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfpmiOldXP'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfpmiNewXP'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfpmiNewXP'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfpmiMixedXP'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfpmiMixedXP'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcUnused2'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbUnused2'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcffactoid'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcffactoid'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcflvcOldXP'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcflvcOldXP'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcflvcNewXP'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcflvcNewXP'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcflvcMixedXP'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcflvcMixedXP'] = self::getInt4d($data, $pos);
            $pos += 4;
        }
        if ($version == self::VERSION_2003) {
            $this->arrayFib['fcHplxsdr'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbHplxsdr'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcSttbfBkmkSdt'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbSttbfBkmkSdt'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfBkfSdt'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfBkfSdt'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfBklSdt'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfBklSdt'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcCustomXForm'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbCustomXForm'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcSttbfBkmkProt'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbSttbfBkmkProt'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfBkfProt'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfBkfProt'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfBklProt'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfBklProt'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcSttbProtUser'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbSttbProtUser'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcUnused'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbUnused'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfpmiOld'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfpmiOld'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfpmiOldInline'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfpmiOldInline'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfpmiNew'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfpmiNew'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfpmiNewInline'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfpmiNewInline'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcflvcOld'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcflvcOld'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcflvcOldInline'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcflvcOldInline'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcflvcNew'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcflvcNew'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcflvcNewInline'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcflvcNewInline'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPgdMother'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPgdMother'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcBkdMother'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbBkdMother'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcAfdMother'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbAfdMother'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPgdFtn'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPgdFtn'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcBkdFtn'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbBkdFtn'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcAfdFtn'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbAfdFtn'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPgdEdn'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPgdEdn'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcBkdEdn'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbBkdEdn'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcAfdEdn'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbAfdEdn'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcAfd'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbAfd'] = self::getInt4d($data, $pos);
            $pos += 4;
        }
        if ($version == self::VERSION_2007) {
            $this->arrayFib['fcPlcfmthd'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfmthd'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcSttbfBkmkMoveFrom'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbSttbfBkmkMoveFrom'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfBkfMoveFrom'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfBkfMoveFrom'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfBklMoveFrom'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfBklMoveFrom'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcSttbfBkmkMoveTo'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbSttbfBkmkMoveTo'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfBkfMoveTo'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfBkfMoveTo'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfBklMoveTo'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfBklMoveTo'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcUnused1'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbUnused1'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcUnused2'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbUnused2'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcUnused3'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbUnused3'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcSttbfBkmkArto'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbSttbfBkmkArto'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfBkfArto'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfBkfArto'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcPlcfBklArto'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbPlcfBklArto'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcArtoData'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbArtoData'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcUnused4'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbUnused4'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcUnused5'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbUnused5'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcUnused6'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbUnused6'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcOssTheme'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbOssTheme'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['fcColorSchemeMapping'] = self::getInt4d($data, $pos);
            $pos += 4;
            $this->arrayFib['lcbColorSchemeMapping'] = self::getInt4d($data, $pos);
            $pos += 4;
        }

        return $pos;
    }

    private function readFibContent(): void
    {
        // Informations about Font
        $this->readRecordSttbfFfn();

        // Informations about page
        $this->readRecordPlcfSed();

        // reading paragraphs
        //@see  https://github.com/notmasteryet/CompoundFile/blob/ec118f354efebdee9102e41b5b7084fce81125b0/WordFileReader/WordDocument.cs#L86
        $this->readRecordPlcfBtePapx();

        // reading character formattings
        //@see  https://github.com/notmasteryet/CompoundFile/blob/ec118f354efebdee9102e41b5b7084fce81125b0/WordFileReader/WordDocument.cs#L94
        $this->readRecordPlcfBteChpx();

        $this->generatePhpWord();
    }

    /**
     * Section and information about them.
     *
     * @see  http://msdn.microsoft.com/en-us/library/dd924458%28v=office.12%29.aspx
     */
    private function readRecordPlcfSed(): void
    {
        $posMem = $this->arrayFib['fcPlcfSed'];
        // PlcfSed
        // PlcfSed : aCP
        $aCP = [];
        $aCP[0] = self::getInt4d($this->data1Table, $posMem);
        $posMem += 4;
        $aCP[1] = self::getInt4d($this->data1Table, $posMem);
        $posMem += 4;

        // PlcfSed : aSed
        //@see  http://msdn.microsoft.com/en-us/library/dd950194%28v=office.12%29.aspx
        $numSed = $this->getNumInLcb($this->arrayFib['lcbPlcfSed'], 12);

        $aSed = [];
        for ($iInc = 0; $iInc < $numSed; ++$iInc) {
            // Sed : http://msdn.microsoft.com/en-us/library/dd950982%28v=office.12%29.aspx
            // fn
            $posMem += 2;
            // fnMpr
            $aSed[$iInc] = self::getInt4d($this->data1Table, $posMem);
            $posMem += 4;
            // fnMpr
            $posMem += 2;
            // fcMpr
            $posMem += 4;
        }

        foreach ($aSed as $offsetSed) {
            // Sepx : http://msdn.microsoft.com/en-us/library/dd921348%28v=office.12%29.aspx
            $cb = self::getInt2d($this->dataWorkDocument, $offsetSed);
            $offsetSed += 2;

            $oStylePrl = $this->readPrl($this->dataWorkDocument, $offsetSed, $cb);
            $offsetSed += $oStylePrl->length;

            $this->arraySections[] = $oStylePrl;
        }
    }

    /**
     * Specifies the fonts that are used in the document.
     *
     * @see  http://msdn.microsoft.com/en-us/library/dd943880%28v=office.12%29.aspx
     */
    private function readRecordSttbfFfn(): void
    {
        $posMem = $this->arrayFib['fcSttbfFfn'];

        $cData = self::getInt2d($this->data1Table, $posMem);
        $posMem += 2;
        $cbExtra = self::getInt2d($this->data1Table, $posMem);
        $posMem += 2;

        if ($cData < 0x7FF0 && $cbExtra == 0) {
            for ($inc = 0; $inc < $cData; ++$inc) {
                // len
                ++$posMem;
                // ffid
                ++$posMem;
                // wWeight (400 : Normal - 700 bold)
                $posMem += 2;
                // chs
                ++$posMem;
                // ixchSzAlt
                $ixchSzAlt = self::getInt1d($this->data1Table, $posMem);
                ++$posMem;
                // panose
                $posMem += 10;
                // fs
                $posMem += 24;
                // xszFfn
                $xszFfn = '';
                do {
                    $char = self::getInt2d($this->data1Table, $posMem);
                    $posMem += 2;
                    if ($char > 0) {
                        $xszFfn .= chr($char);
                    }
                } while ($char != 0);
                // xszAlt
                $xszAlt = '';
                if ($ixchSzAlt > 0) {
                    do {
                        $char = self::getInt2d($this->data1Table, $posMem);
                        $posMem += 2;
                        if ($char == 0) {
                            break;
                        }
                        $xszAlt .= chr($char);
                    } while ($char != 0);
                }
                $this->arrayFonts[] = [
                    'main' => $xszFfn,
                    'alt' => $xszAlt,
                ];
            }
        }
    }

    /**
     * Paragraph and information about them.
     *
     * @see  http://msdn.microsoft.com/en-us/library/dd908569%28v=office.12%29.aspx
     */
    private function readRecordPlcfBtePapx(): void
    {
        $posMem = $this->arrayFib['fcPlcfBtePapx'];
        $num = $this->getNumInLcb($this->arrayFib['lcbPlcfBtePapx'], 4);
        $posMem += 4 * ($num + 1);
        $arrAPnBtePapx = $this->getArrayCP($this->data1Table, $posMem, $num);
        $posMem += 4 * $num;

        foreach ($arrAPnBtePapx as $aPnBtePapx) {
            $offsetBase = $aPnBtePapx * 512;
            $offset = $offsetBase;

            $string = '';

            $numRun = self::getInt1d($this->dataWorkDocument, $offset + 511);
            $arrayRGFC = [];
            for ($inc = 0; $inc <= $numRun; ++$inc) {
                $arrayRGFC[$inc] = self::getInt4d($this->dataWorkDocument, $offset);
                $offset += 4;
            }
            $arrayRGB = [];
            for ($inc = 1; $inc <= $numRun; ++$inc) {
                // @see  http://msdn.microsoft.com/en-us/library/dd925804(v=office.12).aspx
                $arrayRGB[$inc] = self::getInt1d($this->dataWorkDocument, $offset);
                ++$offset;
                // reserved
                $offset += 12;
            }

            foreach (array_keys($arrayRGFC) as $key) {
                if (!isset($arrayRGFC[$key + 1])) {
                    break;
                }
                $strLen = $arrayRGFC[$key + 1] - $arrayRGFC[$key] - 1;
                for ($inc = 0; $inc < ($strLen * 2); ++$inc) {
                    $byte = self::getInt2d($this->dataWorkDocument, $arrayRGFC[$key] + ($inc * 2));
                    if ($byte > 0) {
                        $string .= mb_chr($byte, 'UTF-8');
                    } else {
                        break;
                    }
                }
            }
            $this->arrayParagraphs[] = $string;

            //@todo readPrl for paragraphs
            /*// use $this->readPrl()
            foreach ($arrayRGB as $key => $rgb) {
                $offset = $offsetBase + ($rgb * 2);

                $cb = self::getInt1d($this->dataWorkDocument, $offset);
                $offset += 1;
                print_r('$cb : '.$cb.PHP_EOL);
                if ($cb == 0) {
                    $cb = self::getInt1d($this->dataWorkDocument, $offset);
                    $cb = $cb * 2;
                    $offset += 1;
                    print_r('$cb0 : '.$cb.PHP_EOL);
                } else {
                    $cb = $cb * 2 - 1;
                    print_r('$cbD : '.$cb.PHP_EOL);
                }
                $istd = self::getInt2d($this->dataWorkDocument, $offset);
                $offset += 2;
                $cb -= 2;
                print_r('$istd : '.$istd.($istd == 0 ? ' (Normal)' : '').PHP_EOL);
                if ($cb > 0) {
                    do{
                        $sprm = self::getInt2d($this->dataWorkDocument, $offset);
                        $offset += 2;
                        $cb -= 2;
                        $sprm_IsPmd = $sprm & 0x01FF;
                        $sprm_F = ($sprm/512) & 0x0001;
                        $sprm_Sgc = ($sprm/1024) & 0x0007;
                        $sprm_Spra = ($sprm/8192);

                        print_r('$sprm : 0x'.dechex($sprm).PHP_EOL);
                        print_r('$sprm.ispmd : 0x'.dechex($sprm_IsPmd).PHP_EOL);
                        print_r('$sprm.f : 0x'.dechex($sprm_F).PHP_EOL);
                        print_r('$sprm.sgc : 0x'.dechex($sprm_Sgc));
                        switch (dechex($sprm_Sgc)) {
                            case 0x01:
                                print_r(' (Paragraph property)');
                                break;
                            case 0x02:
                                print_r(' (Character property)');
                                break;
                            case 0x03:
                                print_r(' (Picture property)');
                                break;
                            case 0x04:
                                print_r(' (Section property)');
                                break;
                            case 0x05:
                                print_r(' (Table property)');
                                break;
                        }
                        print_r(PHP_EOL);
                        print_r('$sprm.spra : 0x'.dechex($sprm_Spra).PHP_EOL);
                        switch (dechex($sprm_Spra)) {
                            case 0x0:
                                $operand = self::getInt1d($this->dataWorkDocument, $offset);
                                $offset += 1;
                                $cb -= 1;
                                switch (dechex($operand)) {
                                    case 0x00:
                                        $operand = 'OFF';
                                        break;
                                    case 0x01:
                                        $operand = 'ON';
                                        break;
                                    case 0x80:
                                        $operand = 'CURRENT VALUE';
                                        print_r(''.PHP_EOL.PHP_EOL);
                                        break;
                                    case 0x81:
                                        $operand = 'OPPOSITE OF THE CURRENT VALUE';
                                        break;
                                }
                                break;
                            case 0x1:
                                $operand = self::getInt1d($this->dataWorkDocument, $offset);
                                $offset += 1;
                                $cb -= 1;
                                print_r('$operand : 0x'.dechex($operand).PHP_EOL);
                                break;
                            case 0x2:
                            case 0x4:
                            case 0x5:
                                $operand = self::getInt2d($this->dataWorkDocument, $offset);
                                $offset += 2;
                                $cb -= 2;
                                print_r('$operand : 0x'.dechex($operand).PHP_EOL);
                                break;
                            case 0x3:
                                if ($sprm_IsPmd != 0x70) {
                                    $operand = self::getInt4d($this->dataWorkDocument, $offset);
                                    $offset += 4;
                                    $cb -= 4;
                                    print_r('$operand : 0x'.dechex($operand).PHP_EOL);
                                }
                                break;
                            case 0x7:
                                $operand = self::getInt3d($this->dataWorkDocument, $offset);
                                $offset += 3;
                                $cb -= 3;
                                print_r('$operand : 0x'.dechex($operand).PHP_EOL);
                                break;
                            default:
                                print_r('YO YO YO : '.PHP_EOL);
                        }

                        //
                        switch (dechex($sprm_Sgc)) {
                            case 0x01: // Sprm is modifying a paragraph property.
                                switch ($sprm_IsPmd) {
                                    case 0x0A: // sprmPIlvl
                                        print_r('sprmPIlvl : '.$operand.PHP_EOL.PHP_EOL);
                                        break;
                                    case 0x0B: // sprmPIlfo
                                        print_r('sprmPIlfo : '.$operand.PHP_EOL.PHP_EOL);
                                        break;
                                    default:
                                        print_r('$sprm_IsPmd(1) : '.$sprm_IsPmd.PHP_EOL.PHP_EOL);
                                        break;
                                }
                                break;
                            case 0x02: // Sprm is modifying a character property.
                                switch ($sprm_IsPmd) {
                                    default:
                                        print_r('$sprm_IsPmd(2) : '.$sprm_IsPmd.PHP_EOL.PHP_EOL);
                                        break;
                                }
                                break;
                            case 0x03: // Sprm is modifying a picture property.
                                switch ($sprm_IsPmd) {
                                    default:
                                        print_r('$sprm_IsPmd(3) : '.$sprm_IsPmd.PHP_EOL.PHP_EOL);
                                        break;
                                }
                                break;
                            case 0x04: // Sprm is modifying a section property.
                                switch ($sprm_IsPmd) {
                                    default:
                                        print_r('$sprm_IsPmd(4) : '.$sprm_IsPmd.PHP_EOL.PHP_EOL);
                                        break;
                                }
                                break;
                            case 0x05: // Sprm is modifying a table property.
                                switch ($sprm_IsPmd) {
                                    default:
                                        print_r('$sprm_IsPmd(4) : '.$sprm_IsPmd.PHP_EOL.PHP_EOL);
                                        break;
                                }
                                break;
                            default:
                                print_r('$sprm_Sgc : '.dechex($sprm_Sgc).PHP_EOL.PHP_EOL);
                                break;
                        }
                    } while ($cb > 0);
                } else {
                    if ($istd > 0) {
                        // @todo : Determining Properties of a Paragraph Style
                        # @see  http://msdn.microsoft.com/en-us/library/dd948631%28v=office.12%29.aspx
                    }
                }
            }*/
        }
    }

    /**
     * Character formatting properties to text in a document.
     *
     * @see  http://msdn.microsoft.com/en-us/library/dd907108%28v=office.12%29.aspx
     */
    private function readRecordPlcfBteChpx(): void
    {
        $posMem = $this->arrayFib['fcPlcfBteChpx'];
        $num = $this->getNumInLcb($this->arrayFib['lcbPlcfBteChpx'], 4);
        $aPnBteChpx = [];
        for ($inc = 0; $inc <= $num; ++$inc) {
            $aPnBteChpx[$inc] = self::getInt4d($this->data1Table, $posMem);
            $posMem += 4;
        }
        $pnFkpChpx = self::getInt4d($this->data1Table, $posMem);
        $posMem += 4;

        $offsetBase = $pnFkpChpx * 512;
        $offset = $offsetBase;

        // ChpxFkp
        // @see  http://msdn.microsoft.com/en-us/library/dd910989%28v=office.12%29.aspx
        $numRGFC = self::getInt1d($this->dataWorkDocument, $offset + 511);
        $arrayRGFC = [];
        for ($inc = 0; $inc <= $numRGFC; ++$inc) {
            $arrayRGFC[$inc] = self::getInt4d($this->dataWorkDocument, $offset);
            $offset += 4;
        }

        $arrayRGB = [];
        for ($inc = 1; $inc <= $numRGFC; ++$inc) {
            $arrayRGB[$inc] = self::getInt1d($this->dataWorkDocument, $offset);
            ++$offset;
        }

        $start = 0;
        foreach ($arrayRGB as $keyRGB => $rgb) {
            $oStyle = new stdClass();
            $oStyle->pos_start = $start;
            $oStyle->pos_len = (int) ceil((($arrayRGFC[$keyRGB] - 1) - $arrayRGFC[$keyRGB - 1]) / 2);
            $start += $oStyle->pos_len;

            if ($rgb > 0) {
                // Chp Structure
                // @see  http://msdn.microsoft.com/en-us/library/dd772849%28v=office.12%29.aspx
                $posRGB = $offsetBase + $rgb * 2;

                $cb = self::getInt1d($this->dataWorkDocument, $posRGB);
                ++$posRGB;

                $oStyle->style = $this->readPrl($this->dataWorkDocument, $posRGB, $cb);
                $posRGB += $oStyle->style->length;
            }
            $this->arrayCharacters[] = $oStyle;
        }
    }

    /**
     * @return stdClass
     */
    private function readSprm($sprm)
    {
        $oSprm = new stdClass();
        $oSprm->isPmd = $sprm & 0x01FF;
        $oSprm->f = (int) ($sprm / 512) & 0x0001;
        $oSprm->sgc = (int) ($sprm / 1024) & 0x0007;
        $oSprm->spra = (int) ($sprm / 8192);

        return $oSprm;
    }

    /**
     * @param string $data
     * @param int $pos
     * @param stdClass $oSprm
     *
     * @return array
     */
    private function readSprmSpra($data, $pos, $oSprm)
    {
        $length = 0;
        $operand = null;

        switch (dechex($oSprm->spra)) {
            case 0x0:
                $operand = self::getInt1d($data, $pos);
                $length = 1;
                switch (dechex($operand)) {
                    case 0x00:
                        $operand = false;

                        break;
                    case 0x01:
                        $operand = true;

                        break;
                    case 0x80:
                        $operand = self::SPRA_VALUE;

                        break;
                    case 0x81:
                        $operand = self::SPRA_VALUE_OPPOSITE;

                        break;
                }

                break;
            case 0x1:
                $operand = self::getInt1d($data, $pos);
                $length = 1;

                break;
            case 0x2:
            case 0x4:
            case 0x5:
                $operand = self::getInt2d($data, $pos);
                $length = 2;

                break;
            case 0x3:
                if ($oSprm->isPmd != 0x70) {
                    $operand = self::getInt4d($data, $pos);
                    $length = 4;
                }

                break;
            case 0x7:
                $operand = self::getInt3d($data, $pos);
                $length = 3;

                break;
            default:
                // print_r('YO YO YO : '.PHP_EOL);
        }

        return [
            'length' => $length,
            'operand' => $operand,
        ];
    }

    /**
     * @param $data int
     * @param $pos int
     * @param $cbNum int
     *
     * @return stdClass
     *
     * @see  http://msdn.microsoft.com/en-us/library/dd772849%28v=office.12%29.aspx
     */
    private function readPrl($data, $pos, $cbNum)
    {
        $posStart = $pos;
        $oStylePrl = new stdClass();

        // Variables
        $sprmCPicLocation = null;
        $sprmCFData = null;
        //$sprmCFSpec = null;

        do {
            // Variables
            $operand = null;

            $sprm = self::getInt2d($data, $pos);
            $oSprm = $this->readSprm($sprm);
            $pos += 2;
            $cbNum -= 2;

            $arrayReturn = $this->readSprmSpra($data, $pos, $oSprm);
            $pos += $arrayReturn['length'];
            $cbNum -= $arrayReturn['length'];
            $operand = $arrayReturn['operand'];

            switch (dechex($oSprm->sgc)) {
                // Paragraph property
                case 0x01:
                    break;
                    // Character property
                case 0x02:
                    if (!isset($oStylePrl->styleFont)) {
                        $oStylePrl->styleFont = [];
                    }
                    switch ($oSprm->isPmd) {
                        // sprmCFRMarkIns
                        case 0x01:
                            break;
                            // sprmCFFldVanish
                        case 0x02:
                            break;
                            // sprmCPicLocation
                        case 0x03:
                            $sprmCPicLocation = $operand;

                            break;
                            // sprmCFData
                        case 0x06:
                            $sprmCFData = dechex($operand) != 0x00;

                            break;
                            // sprmCFItalic
                        case 0x36:
                            // By default, text is not italicized.
                            switch ($operand) {
                                case false:
                                case true:
                                    $oStylePrl->styleFont['italic'] = $operand;

                                    break;
                                case self::SPRA_VALUE:
                                    $oStylePrl->styleFont['italic'] = false;

                                    break;
                                case self::SPRA_VALUE_OPPOSITE:
                                    $oStylePrl->styleFont['italic'] = true;

                                    break;
                            }

                            break;
                            // sprmCIstd
                        case 0x30:
                            //print_r('sprmCIstd : '.dechex($operand).PHP_EOL.PHP_EOL);
                            break;
                            // sprmCFBold
                        case 0x35:
                            // By default, text is not bold.
                            switch ($operand) {
                                case false:
                                case true:
                                    $oStylePrl->styleFont['bold'] = $operand;

                                    break;
                                case self::SPRA_VALUE:
                                    $oStylePrl->styleFont['bold'] = false;

                                    break;
                                case self::SPRA_VALUE_OPPOSITE:
                                    $oStylePrl->styleFont['bold'] = true;

                                    break;
                            }

                            break;
                            // sprmCFStrike
                        case 0x37:
                            // By default, text is not struck through.
                            switch ($operand) {
                                case false:
                                case true:
                                    $oStylePrl->styleFont['strikethrough'] = $operand;

                                    break;
                                case self::SPRA_VALUE:
                                    $oStylePrl->styleFont['strikethrough'] = false;

                                    break;
                                case self::SPRA_VALUE_OPPOSITE:
                                    $oStylePrl->styleFont['strikethrough'] = true;

                                    break;
                            }

                            break;
                            // sprmCKul
                        case 0x3E:
                            switch (dechex($operand)) {
                                case 0x00:
                                    $oStylePrl->styleFont['underline'] = Style\Font::UNDERLINE_NONE;

                                    break;
                                case 0x01:
                                    $oStylePrl->styleFont['underline'] = Style\Font::UNDERLINE_SINGLE;

                                    break;
                                case 0x02:
                                    $oStylePrl->styleFont['underline'] = Style\Font::UNDERLINE_WORDS;

                                    break;
                                case 0x03:
                                    $oStylePrl->styleFont['underline'] = Style\Font::UNDERLINE_DOUBLE;

                                    break;
                                case 0x04:
                                    $oStylePrl->styleFont['underline'] = Style\Font::UNDERLINE_DOTTED;

                                    break;
                                case 0x06:
                                    $oStylePrl->styleFont['underline'] = Style\Font::UNDERLINE_HEAVY;

                                    break;
                                case 0x07:
                                    $oStylePrl->styleFont['underline'] = Style\Font::UNDERLINE_DASH;

                                    break;
                                case 0x09:
                                    $oStylePrl->styleFont['underline'] = Style\Font::UNDERLINE_DOTDASH;

                                    break;
                                case 0x0A:
                                    $oStylePrl->styleFont['underline'] = Style\Font::UNDERLINE_DOTDOTDASH;

                                    break;
                                case 0x0B:
                                    $oStylePrl->styleFont['underline'] = Style\Font::UNDERLINE_WAVY;

                                    break;
                                case 0x14:
                                    $oStylePrl->styleFont['underline'] = Style\Font::UNDERLINE_DOTTEDHEAVY;

                                    break;
                                case 0x17:
                                    $oStylePrl->styleFont['underline'] = Style\Font::UNDERLINE_DASHHEAVY;

                                    break;
                                case 0x19:
                                    $oStylePrl->styleFont['underline'] = Style\Font::UNDERLINE_DOTDASHHEAVY;

                                    break;
                                case 0x1A:
                                    $oStylePrl->styleFont['underline'] = Style\Font::UNDERLINE_DOTDOTDASHHEAVY;

                                    break;
                                case 0x1B:
                                    $oStylePrl->styleFont['underline'] = Style\Font::UNDERLINE_WAVYHEAVY;

                                    break;
                                case 0x27:
                                    $oStylePrl->styleFont['underline'] = Style\Font::UNDERLINE_DASHLONG;

                                    break;
                                case 0x2B:
                                    $oStylePrl->styleFont['underline'] = Style\Font::UNDERLINE_WAVYDOUBLE;

                                    break;
                                case 0x37:
                                    $oStylePrl->styleFont['underline'] = Style\Font::UNDERLINE_DASHLONGHEAVY;

                                    break;
                                default:
                                    $oStylePrl->styleFont['underline'] = Style\Font::UNDERLINE_NONE;

                                    break;
                            }

                            break;
                            // sprmCIco
                            //@see  http://msdn.microsoft.com/en-us/library/dd773060%28v=office.12%29.aspx
                        case 0x42:
                            switch (dechex($operand)) {
                                case 0x00:
                                case 0x01:
                                    $oStylePrl->styleFont['color'] = '000000';

                                    break;
                                case 0x02:
                                    $oStylePrl->styleFont['color'] = '0000FF';

                                    break;
                                case 0x03:
                                    $oStylePrl->styleFont['color'] = '00FFFF';

                                    break;
                                case 0x04:
                                    $oStylePrl->styleFont['color'] = '00FF00';

                                    break;
                                case 0x05:
                                    $oStylePrl->styleFont['color'] = 'FF00FF';

                                    break;
                                case 0x06:
                                    $oStylePrl->styleFont['color'] = 'FF0000';

                                    break;
                                case 0x07:
                                    $oStylePrl->styleFont['color'] = 'FFFF00';

                                    break;
                                case 0x08:
                                    $oStylePrl->styleFont['color'] = 'FFFFFF';

                                    break;
                                case 0x09:
                                    $oStylePrl->styleFont['color'] = '000080';

                                    break;
                                case 0x0A:
                                    $oStylePrl->styleFont['color'] = '008080';

                                    break;
                                case 0x0B:
                                    $oStylePrl->styleFont['color'] = '008000';

                                    break;
                                case 0x0C:
                                    $oStylePrl->styleFont['color'] = '800080';

                                    break;
                                case 0x0D:
                                    $oStylePrl->styleFont['color'] = '800080';

                                    break;
                                case 0x0E:
                                    $oStylePrl->styleFont['color'] = '808000';

                                    break;
                                case 0x0F:
                                    $oStylePrl->styleFont['color'] = '808080';

                                    break;
                                case 0x10:
                                    $oStylePrl->styleFont['color'] = 'C0C0C0';
                            }

                            break;
                            // sprmCHps
                        case 0x43:
                            $oStylePrl->styleFont['size'] = $operand / 2;

                            break;
                            // sprmCIss
                        case 0x48:
                            if (!isset($oStylePrl->styleFont['superScript'])) {
                                $oStylePrl->styleFont['superScript'] = false;
                            }
                            if (!isset($oStylePrl->styleFont['subScript'])) {
                                $oStylePrl->styleFont['subScript'] = false;
                            }
                            switch (dechex($operand)) {
                                case 0x00:
                                    // Normal text
                                    break;
                                case 0x01:
                                    $oStylePrl->styleFont['superScript'] = true;

                                    break;
                                case 0x02:
                                    $oStylePrl->styleFont['subScript'] = true;

                                    break;
                            }

                            break;
                            // sprmCRgFtc0
                        case 0x4F:
                            $oStylePrl->styleFont['name'] = '';
                            if (isset($this->arrayFonts[$operand])) {
                                $oStylePrl->styleFont['name'] = $this->arrayFonts[$operand]['main'];
                            }

                            break;
                            // sprmCRgFtc1
                        case 0x50:
                            // if the language for the text is an East Asian language
                            break;
                            // sprmCRgFtc2
                        case 0x51:
                            // if the character falls outside the Unicode character range
                            break;
                            // sprmCFSpec
                        case 0x55:
                            //$sprmCFSpec = $operand;
                            break;
                            // sprmCFtcBi
                        case 0x5E:
                            break;
                            // sprmCFItalicBi
                        case 0x5D:
                            break;
                            // sprmCHpsBi
                        case 0x61:
                            break;
                            // sprmCShd80
                            //@see  http://msdn.microsoft.com/en-us/library/dd923447%28v=office.12%29.aspx
                        case 0x66:
                            // $operand = self::getInt2d($data, $pos);
                            $pos += 2;
                            $cbNum -= 2;

                            // $ipat = ($operand >> 0) && bindec('111111');
                            // $icoBack = ($operand >> 6) && bindec('11111');
                            // $icoFore = ($operand >> 11) && bindec('11111');
                            break;
                            // sprmCCv
                            //@see  http://msdn.microsoft.com/en-us/library/dd952824%28v=office.12%29.aspx
                        case 0x70:
                            $red = str_pad(dechex(self::getInt1d($this->dataWorkDocument, $pos)), 2, '0', STR_PAD_LEFT);
                            ++$pos;
                            $green = str_pad(dechex(self::getInt1d($this->dataWorkDocument, $pos)), 2, '0', STR_PAD_LEFT);
                            ++$pos;
                            $blue = str_pad(dechex(self::getInt1d($this->dataWorkDocument, $pos)), 2, '0', STR_PAD_LEFT);
                            ++$pos;
                            ++$pos;
                            $oStylePrl->styleFont['color'] = $red . $green . $blue;
                            $cbNum -= 4;

                            break;
                        default:
                            // print_r('@todo Character : 0x'.dechex($oSprm->isPmd));
                            // print_r(PHP_EOL);
                    }

                    break;
                    // Picture property
                case 0x03:
                    break;
                    // Section property
                case 0x04:
                    if (!isset($oStylePrl->styleSection)) {
                        $oStylePrl->styleSection = [];
                    }
                    switch ($oSprm->isPmd) {
                        // sprmSNfcPgn
                        case 0x0E:
                            // numbering format used for page numbers
                            break;
                            // sprmSXaPage
                        case 0x1F:
                            $oStylePrl->styleSection['pageSizeW'] = $operand;

                            break;
                            // sprmSYaPage
                        case 0x20:
                            $oStylePrl->styleSection['pageSizeH'] = $operand;

                            break;
                            // sprmSDxaLeft
                        case 0x21:
                            $oStylePrl->styleSection['marginLeft'] = $operand;

                            break;
                            // sprmSDxaRight
                        case 0x22:
                            $oStylePrl->styleSection['marginRight'] = $operand;

                            break;
                            // sprmSDyaTop
                        case 0x23:
                            $oStylePrl->styleSection['marginTop'] = $operand;

                            break;
                            // sprmSDyaBottom
                        case 0x24:
                            $oStylePrl->styleSection['marginBottom'] = $operand;

                            break;
                            // sprmSFBiDi
                        case 0x28:
                            // RTL layout
                            break;
                            // sprmSDxtCharSpace
                        case 0x30:
                            // characpter pitch
                            break;
                            // sprmSDyaLinePitch
                        case 0x31:
                            // line height
                            break;
                            // sprmSClm
                        case 0x32:
                            // document grid mode
                            break;
                            // sprmSTextFlow
                        case 0x33:
                            // text flow
                            break;
                        default:
                            // print_r('@todo Section : 0x'.dechex($oSprm->isPmd));
                            // print_r(PHP_EOL);
                    }

                    break;
                    // Table property
                case 0x05:
                    break;
            }
        } while ($cbNum > 0);

        if (null !== $sprmCPicLocation) {
            if (null !== $sprmCFData && $sprmCFData == 0x01) {
                // NilPICFAndBinData
                //@todo Read Hyperlink structure
                /*$lcb = self::getInt4d($this->dataData, $sprmCPicLocation);
                $sprmCPicLocation += 4;
                $cbHeader = self::getInt2d($this->dataData, $sprmCPicLocation);
                $sprmCPicLocation += 2;
                // ignored
                $sprmCPicLocation += 62;
                // depending of the element
                // Hyperlink => HFD
                // HFD > bits
                $sprmCPicLocation += 1;
                // HFD > clsid
                $sprmCPicLocation += 16;
                // HFD > hyperlink
                //@see  http://msdn.microsoft.com/en-us/library/dd909835%28v=office.12%29.aspx
                $streamVersion = self::getInt4d($this->dataData, $sprmCPicLocation);
                $sprmCPicLocation += 4;
                $data = self::getInt4d($this->dataData, $sprmCPicLocation);
                $sprmCPicLocation += 4;
                $hlstmfAbsFromGetdataRel = ($data >> 9) & bindec('1');
                $hlstmfMonikerSavedAsStr = ($data >> 8) & bindec('1');
                $hlstmfHasFrameName = ($data >> 7) & bindec('1');
                $hlstmfHasCreationTime = ($data >> 6) & bindec('1');
                $hlstmfHasGUID = ($data >> 5) & bindec('1');
                $hlstmfHasDisplayName = ($data >> 4) & bindec('1');
                $hlstmfHasLocationStr = ($data >> 3) & bindec('1');
                $hlstmfSiteGaveDisplayName = ($data >> 2) & bindec('1');
                $hlstmfIsAbsolute = ($data >> 1) & bindec('1');
                $hlstmfHasMoniker = ($data >> 0) & bindec('1');
                for ($inc = 0; $inc <= 32; $inc++) {
                    echo ($data >> $inc) & bindec('1');
                }

                print_r('$hlstmfHasMoniker > '.$hlstmfHasMoniker.PHP_EOL);
                print_r('$hlstmfIsAbsolute > '.$hlstmfIsAbsolute.PHP_EOL);
                print_r('$hlstmfSiteGaveDisplayName > '.$hlstmfSiteGaveDisplayName.PHP_EOL);
                print_r('$hlstmfHasLocationStr > '.$hlstmfHasLocationStr.PHP_EOL);
                print_r('$hlstmfHasDisplayName > '.$hlstmfHasDisplayName.PHP_EOL);
                print_r('$hlstmfHasGUID > '.$hlstmfHasGUID.PHP_EOL);
                print_r('$hlstmfHasCreationTime > '.$hlstmfHasCreationTime.PHP_EOL);
                print_r('$hlstmfHasFrameName > '.$hlstmfHasFrameName.PHP_EOL);
                print_r('$hlstmfMonikerSavedAsStr > '.$hlstmfMonikerSavedAsStr.PHP_EOL);
                print_r('$hlstmfAbsFromGetdataRel > '.$hlstmfAbsFromGetdataRel.PHP_EOL);
                if ($streamVersion == 2) {
                    $AAA = self::getInt4d($this->dataData, $sprmCPicLocation);
                    echo 'AAAA : '.$AAA.PHP_EOL;
                    if ($hlstmfHasDisplayName == 1) {
                        echo 'displayName'.PHP_EOL;
                    }
                    if ($hlstmfHasFrameName == 1) {
                        echo 'targetFrameName'.PHP_EOL;
                    }
                    if ($hlstmfHasMoniker == 1 || $hlstmfMonikerSavedAsStr == 1) {
                        $sprmCPicLocation += 16;
                        $length = self::getInt4d($this->dataData, $sprmCPicLocation);
                        $sprmCPicLocation += 4;
                        for ($inc = 0; $inc < ($length / 2); $inc++) {
                            $chr = self::getInt2d($this->dataData, $sprmCPicLocation);
                            $sprmCPicLocation += 2;
                            print_r(chr($chr));
                        }
                        echo PHP_EOL;
                        echo 'moniker : '.$length.PHP_EOL;
                    }
                    if ($hlstmfHasMoniker == 1 || $hlstmfMonikerSavedAsStr == 1) {
                        echo 'oleMoniker'.PHP_EOL;
                    }
                    if ($hlstmfHasLocationStr == 1) {
                        echo 'location'.PHP_EOL;
                    }
                    if ($hlstmfHasGUID == 1) {
                        echo 'guid'.PHP_EOL;
                        $sprmCPicLocation += 16;
                    }
                    if ($hlstmfHasCreationTime == 1) {
                        echo 'fileTime'.PHP_EOL;
                        $sprmCPicLocation += 4;
                    }
                    echo 'HYPERLINK'.PHP_EOL;
                }*/
            } else {
                // Pictures
                //@see  http://msdn.microsoft.com/en-us/library/dd925458%28v=office.12%29.aspx
                //@see  http://msdn.microsoft.com/en-us/library/dd926136%28v=office.12%29.aspx
                // PICF : lcb
                $sprmCPicLocation += 4;
                // PICF : cbHeader
                $sprmCPicLocation += 2;
                // PICF : mfpf : mm
                $mfpfMm = self::getInt2d($this->dataData, $sprmCPicLocation);
                $sprmCPicLocation += 2;
                // PICF : mfpf : xExt
                $sprmCPicLocation += 2;
                // PICF : mfpf : yExt
                $sprmCPicLocation += 2;
                // PICF : mfpf : swHMF
                $sprmCPicLocation += 2;
                // PICF : innerHeader : grf
                $sprmCPicLocation += 4;
                // PICF : innerHeader : padding1
                $sprmCPicLocation += 4;
                // PICF : innerHeader : mmPM
                $sprmCPicLocation += 2;
                // PICF : innerHeader : padding2
                $sprmCPicLocation += 4;
                // PICF : picmid : dxaGoal
                $picmidDxaGoal = self::getInt2d($this->dataData, $sprmCPicLocation);
                $sprmCPicLocation += 2;
                // PICF : picmid : dyaGoal
                $picmidDyaGoal = self::getInt2d($this->dataData, $sprmCPicLocation);
                $sprmCPicLocation += 2;
                // PICF : picmid : mx
                $picmidMx = self::getInt2d($this->dataData, $sprmCPicLocation);
                $sprmCPicLocation += 2;
                // PICF : picmid : my
                $picmidMy = self::getInt2d($this->dataData, $sprmCPicLocation);
                $sprmCPicLocation += 2;
                // PICF : picmid : dxaReserved1
                $picmidDxaCropLeft = self::getInt2d($this->dataData, $sprmCPicLocation);
                $sprmCPicLocation += 2;
                // PICF : picmid : dyaReserved1
                $picmidDxaCropTop = self::getInt2d($this->dataData, $sprmCPicLocation);
                $sprmCPicLocation += 2;
                // PICF : picmid : dxaReserved2
                $picmidDxaCropRight = self::getInt2d($this->dataData, $sprmCPicLocation);
                $sprmCPicLocation += 2;
                // PICF : picmid : dyaReserved2
                $picmidDxaCropBottom = self::getInt2d($this->dataData, $sprmCPicLocation);
                $sprmCPicLocation += 2;
                // PICF : picmid : fReserved
                ++$sprmCPicLocation;
                // PICF : picmid : bpp
                ++$sprmCPicLocation;
                // PICF : picmid : brcTop80
                $sprmCPicLocation += 4;
                // PICF : picmid : brcLeft80
                $sprmCPicLocation += 4;
                // PICF : picmid : brcBottom80
                $sprmCPicLocation += 4;
                // PICF : picmid : brcRight80
                $sprmCPicLocation += 4;
                // PICF : picmid : dxaReserved3
                $sprmCPicLocation += 2;
                // PICF : picmid : dyaReserved3
                $sprmCPicLocation += 2;
                // PICF : cProps
                $sprmCPicLocation += 2;

                if ($mfpfMm == 0x0066) {
                    // cchPicName
                    $cchPicName = self::getInt1d($this->dataData, $sprmCPicLocation);
                    ++$sprmCPicLocation;

                    // stPicName
                    //$stPicName = '';
                    for ($inc = 0; $inc <= $cchPicName; ++$inc) {
                        //$chr = self::getInt1d($this->dataData, $sprmCPicLocation);
                        ++$sprmCPicLocation;
                        //$stPicName .= chr($chr);
                    }
                }

                // picture (OfficeArtInlineSpContainer)
                // picture : shape
                $shapeRH = $this->loadRecordHeader($this->dataData, $sprmCPicLocation);
                $sprmCPicLocation += 8;
                if ($shapeRH['recVer'] == 0xF && $shapeRH['recInstance'] == 0x000 && $shapeRH['recType'] == 0xF004) {
                    $sprmCPicLocation += $shapeRH['recLen'];
                }
                // picture : rgfb
                //@see  http://msdn.microsoft.com/en-us/library/dd950560%28v=office.12%29.aspx
                $fileBlockRH = $this->loadRecordHeader($this->dataData, $sprmCPicLocation);
                while ($fileBlockRH['recType'] == 0xF007 || ($fileBlockRH['recType'] >= 0xF018 && $fileBlockRH['recType'] <= 0xF117)) {
                    $sprmCPicLocation += 8;
                    switch ($fileBlockRH['recType']) {
                        // OfficeArtFBSE
                        //@see  http://msdn.microsoft.com/en-us/library/dd944923%28v=office.12%29.aspx
                        case 0xF007:
                            // btWin32
                            ++$sprmCPicLocation;
                            // btMacOS
                            ++$sprmCPicLocation;
                            // rgbUid
                            $sprmCPicLocation += 16;
                            // tag
                            $sprmCPicLocation += 2;
                            // size
                            $sprmCPicLocation += 4;
                            // cRef
                            $sprmCPicLocation += 4;
                            // foDelay
                            $sprmCPicLocation += 4;
                            // unused1
                            ++$sprmCPicLocation;
                            // cbName
                            $cbName = self::getInt1d($this->dataData, $sprmCPicLocation);
                            ++$sprmCPicLocation;
                            // unused2
                            ++$sprmCPicLocation;
                            // unused3
                            ++$sprmCPicLocation;
                            // nameData
                            if ($cbName > 0) {
                                //$nameData = '';
                                for ($inc = 0; $inc <= ($cbName / 2); ++$inc) {
                                    //$chr = self::getInt2d($this->dataData, $sprmCPicLocation);
                                    $sprmCPicLocation += 2;
                                    //$nameData .= chr($chr);
                                }
                            }
                            // embeddedBlip
                            //@see  http://msdn.microsoft.com/en-us/library/dd910081%28v=office.12%29.aspx
                            $embeddedBlipRH = $this->loadRecordHeader($this->dataData, $sprmCPicLocation);
                            switch ($embeddedBlipRH['recType']) {
                                case self::OFFICEARTBLIPJPG:
                                case self::OFFICEARTBLIPJPEG:
                                    if (!isset($oStylePrl->image)) {
                                        $oStylePrl->image = [];
                                    }
                                    $sprmCPicLocation += 8;
                                    // embeddedBlip : rgbUid1
                                    $sprmCPicLocation += 16;
                                    if ($embeddedBlipRH['recInstance'] == 0x6E1) {
                                        // rgbUid2
                                        $sprmCPicLocation += 16;
                                    }
                                    // embeddedBlip : tag
                                    ++$sprmCPicLocation;
                                    // embeddedBlip : BLIPFileData
                                    $oStylePrl->image['data'] = substr($this->dataData, $sprmCPicLocation, $embeddedBlipRH['recLen']);
                                    $oStylePrl->image['format'] = 'jpg';
                                    // Image Size
                                    $iCropWidth = $picmidDxaGoal - ($picmidDxaCropLeft + $picmidDxaCropRight);
                                    $iCropHeight = $picmidDyaGoal - ($picmidDxaCropTop + $picmidDxaCropBottom);
                                    if (!$iCropWidth) {
                                        $iCropWidth = 1;
                                    }
                                    if (!$iCropHeight) {
                                        $iCropHeight = 1;
                                    }
                                    $oStylePrl->image['width'] = Drawing::twipsToPixels($iCropWidth * $picmidMx / 1000);
                                    $oStylePrl->image['height'] = Drawing::twipsToPixels($iCropHeight * $picmidMy / 1000);

                                    $sprmCPicLocation += $embeddedBlipRH['recLen'];

                                    break;
                                case self::OFFICEARTBLIPPNG:
                                    break;
                                default:
                                    // print_r(dechex($embeddedBlipRH['recType']));
                            }

                            break;
                    }
                    $fileBlockRH = $this->loadRecordHeader($this->dataData, $sprmCPicLocation);
                }
            }
        }

        $oStylePrl->length = $pos - $posStart;

        return $oStylePrl;
    }

    /**
     * Read a record header.
     *
     * @param string $stream
     * @param int $pos
     *
     * @return array
     */
    private function loadRecordHeader($stream, $pos)
    {
        $rec = self::getInt2d($stream, $pos);
        $recType = self::getInt2d($stream, $pos + 2);
        $recLen = self::getInt4d($stream, $pos + 4);

        return [
            'recVer' => ($rec >> 0) & bindec('1111'),
            'recInstance' => ($rec >> 4) & bindec('111111111111'),
            'recType' => $recType,
            'recLen' => $recLen,
        ];
    }

    private function generatePhpWord(): void
    {
        foreach ($this->arraySections as $itmSection) {
            $oSection = $this->phpWord->addSection();
            $oSection->setStyle($itmSection->styleSection);

            $sHYPERLINK = '';
            foreach ($this->arrayParagraphs as $itmParagraph) {
                $textPara = $itmParagraph;
                foreach ($this->arrayCharacters as $oCharacters) {
                    $subText = mb_substr($textPara, $oCharacters->pos_start, $oCharacters->pos_len);
                    $subText = str_replace(chr(13), PHP_EOL, $subText);
                    $arrayText = explode(PHP_EOL, $subText);
                    if (end($arrayText) == '') {
                        array_pop($arrayText);
                    }
                    if (reset($arrayText) == '') {
                        array_shift($arrayText);
                    }

                    // Style Character
                    $styleFont = [];
                    if (isset($oCharacters->style)) {
                        if (isset($oCharacters->style->styleFont)) {
                            $styleFont = $oCharacters->style->styleFont;
                        }
                    }

                    foreach ($arrayText as $sText) {
                        // HyperLink
                        if (empty($sText) && !empty($sHYPERLINK)) {
                            $arrHYPERLINK = explode('"', $sHYPERLINK);
                            $oSection->addLink($arrHYPERLINK[1], null);
                            // print_r('>addHyperLink<'.$sHYPERLINK.'>'.ord($sHYPERLINK[0]).EOL);
                            $sHYPERLINK = '';
                        }

                        // TextBreak
                        if (empty($sText)) {
                            $oSection->addTextBreak();
                            $sHYPERLINK = '';
                            // print_r('>addTextBreak<' . EOL);
                        }

                        if (!empty($sText)) {
                            if (!empty($sHYPERLINK) && ord($sText[0]) > 20) {
                                $sHYPERLINK .= $sText;
                            }
                            if (empty($sHYPERLINK)) {
                                if (ord($sText[0]) > 20) {
                                    if (strpos(trim($sText), 'HYPERLINK "') === 0) {
                                        $sHYPERLINK = $sText;
                                    } else {
                                        $oSection->addText($sText, $styleFont);
                                        // print_r('>addText<'.$sText.'>'.ord($sText[0]).EOL);
                                    }
                                }
                                if (ord($sText[0]) == 1) {
                                    if (isset($oCharacters->style->image)) {
                                        $fileImage = tempnam(sys_get_temp_dir(), 'PHPWord_MsDoc') . '.' . $oCharacters->style->image['format'];
                                        file_put_contents($fileImage, $oCharacters->style->image['data']);
                                        $oSection->addImage($fileImage, ['width' => $oCharacters->style->image['width'], 'height' => $oCharacters->style->image['height']]);
                                        // print_r('>addImage<'.$fileImage.'>'.EOL);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Read 8-bit unsigned integer.
     *
     * @param string $data
     * @param int $pos
     *
     * @return int
     */
    public static function getInt1d($data, $pos)
    {
        return ord($data[$pos]);
    }

    /**
     * Read 16-bit unsigned integer.
     *
     * @param string $data
     * @param int $pos
     *
     * @return int
     */
    public static function getInt2d($data, $pos)
    {
        return ord($data[$pos]) | (ord($data[$pos + 1]) << 8);
    }

    /**
     * Read 24-bit signed integer.
     *
     * @param string $data
     * @param int $pos
     *
     * @return int
     */
    public static function getInt3d($data, $pos)
    {
        return ord($data[$pos]) | (ord($data[$pos + 1]) << 8) | (ord($data[$pos + 2]) << 16);
    }

    /**
     * Read 32-bit signed integer.
     *
     * @param string $data
     * @param int $pos
     *
     * @return int
     */
    public static function getInt4d($data, $pos)
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
