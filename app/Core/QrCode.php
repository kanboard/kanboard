<?php

namespace Kanboard\Core;

use InvalidArgumentException;

/**
 * QR Code encoder (byte mode, versions 1 to 40, all error correction levels)
 *
 * Implemented from ISO/IEC 18004.
 *
 * @package core
 * @author  Frederic Guillot
 */
class QrCode
{
    const LEVEL_L = 'L';
    const LEVEL_M = 'M';
    const LEVEL_Q = 'Q';
    const LEVEL_H = 'H';

    const PENALTY_N1 = 3;
    const PENALTY_N2 = 3;
    const PENALTY_N3 = 40;
    const PENALTY_N4 = 10;

    /**
     * Error correction codewords per block, indexed by level then version (index 0 is unused)
     */
    const ECC_CODEWORDS_PER_BLOCK = [
        'L' => [0, 7, 10, 15, 20, 26, 18, 20, 24, 30, 18, 20, 24, 26, 30, 22, 24, 28, 30, 28, 28, 28, 28, 30, 30, 26, 28, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30],
        'M' => [0, 10, 16, 26, 18, 24, 16, 18, 22, 22, 26, 30, 22, 22, 24, 24, 28, 28, 26, 26, 26, 26, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28],
        'Q' => [0, 13, 22, 18, 26, 18, 24, 18, 22, 20, 24, 28, 26, 24, 20, 30, 24, 28, 28, 26, 30, 28, 30, 30, 30, 30, 28, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30],
        'H' => [0, 17, 28, 22, 16, 22, 28, 26, 26, 24, 28, 24, 28, 22, 24, 24, 30, 28, 28, 26, 28, 30, 24, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30],
    ];

    /**
     * Number of error correction blocks, indexed by level then version (index 0 is unused)
     */
    const ECC_BLOCKS = [
        'L' => [0, 1, 1, 1, 1, 1, 2, 2, 2, 2, 4, 4, 4, 4, 4, 6, 6, 6, 6, 7, 8, 8, 9, 9, 10, 12, 12, 12, 13, 14, 15, 16, 17, 18, 19, 19, 20, 21, 22, 24, 25],
        'M' => [0, 1, 1, 1, 2, 2, 4, 4, 4, 5, 5, 5, 8, 9, 9, 10, 10, 11, 13, 14, 16, 17, 17, 18, 20, 21, 23, 25, 26, 28, 29, 31, 33, 35, 37, 38, 40, 43, 45, 47, 49],
        'Q' => [0, 1, 1, 2, 2, 4, 4, 6, 6, 8, 8, 8, 10, 12, 16, 12, 17, 16, 18, 21, 20, 23, 23, 25, 27, 29, 34, 34, 35, 38, 40, 43, 45, 48, 51, 53, 56, 59, 62, 65, 68],
        'H' => [0, 1, 1, 2, 4, 4, 4, 5, 6, 8, 8, 11, 11, 16, 16, 18, 16, 19, 21, 25, 25, 25, 34, 30, 32, 35, 37, 40, 42, 45, 48, 51, 54, 57, 60, 63, 66, 70, 74, 77, 81],
    ];

    /**
     * Error correction level indicator used in the format information
     */
    const FORMAT_LEVEL_BITS = ['L' => 1, 'M' => 0, 'Q' => 3, 'H' => 2];

    /**
     * ECI mode indicator followed by the UTF-8 designator (26)
     */
    const UTF8_ECI_HEADER = '011100011010';

    private static array $gfExp = [];
    private static array $gfLog = [];

    private array $modules = [];
    private array $reserved = [];
    private int $size = 0;

    public function __construct(private readonly string $level = self::LEVEL_L)
    {
        if (! isset(self::FORMAT_LEVEL_BITS[$level])) {
            throw new InvalidArgumentException('Invalid error correction level');
        }
    }

    /**
     * Encode data and return the matrix of modules (true is dark)
     *
     * @access public
     * @param  string $data
     * @return bool[][]
     */
    public function encode(string $data): array
    {
        // Without an ECI header, decoders have to guess the charset of non-ASCII data
        $header = ! mb_check_encoding($data, 'ASCII') && mb_check_encoding($data, 'UTF-8') ? self::UTF8_ECI_HEADER : '';
        $version = $this->getVersion(strlen($header), strlen($data));
        $this->size = $version * 4 + 17;
        $this->modules = array_fill(0, $this->size, array_fill(0, $this->size, false));
        $this->reserved = $this->modules;

        $this->drawFunctionPatterns($version);
        $this->drawCodewords($this->addErrorCorrection($this->getDataCodewords($header, $data, $version), $version));
        $this->applyBestMask();

        return $this->modules;
    }

    /**
     * Encode data and render it as an SVG image
     *
     * @access public
     * @param  string  $data
     * @param  integer $moduleSize  Size of a module in pixels
     * @param  integer $margin      Quiet zone in modules
     * @return string
     */
    public function renderSvg(string $data, int $moduleSize = 6, int $margin = 4): string
    {
        $matrix = $this->encode($data);
        $size = count($matrix);
        $path = '';

        foreach ($matrix as $y => $row) {
            for ($x = 0; $x < $size; $x++) {
                if ($row[$x]) {
                    $start = $x;

                    while ($x + 1 < $size && $row[$x + 1]) {
                        $x++;
                    }

                    $length = $x - $start + 1;
                    $path .= 'M'.($start + $margin).' '.($y + $margin).'h'.$length.'v1h-'.$length.'z';
                }
            }
        }

        $viewBox = $size + 2 * $margin;
        $pixels = $viewBox * $moduleSize;

        return '<?xml version="1.0" encoding="UTF-8"?>'."\n".
            '<svg xmlns="http://www.w3.org/2000/svg" width="'.$pixels.'" height="'.$pixels.'" viewBox="0 0 '.$viewBox.' '.$viewBox.'" shape-rendering="crispEdges">'.
            '<rect width="'.$viewBox.'" height="'.$viewBox.'" fill="#fff"/>'.
            '<path fill="#000" d="'.$path.'"/>'.
            '</svg>';
    }

    /**
     * Find the smallest version that can hold the data
     *
     * @access private
     * @param  integer $headerBits
     * @param  integer $length
     * @return integer
     */
    private function getVersion(int $headerBits, int $length): int
    {
        for ($version = 1; $version <= 40; $version++) {
            if ($headerBits + 4 + $this->getCharacterCountBits($version) + $length * 8 <= $this->getDataCodewordCount($version) * 8) {
                return $version;
            }
        }

        throw new InvalidArgumentException('Data too long for a QR code');
    }

    private function getCharacterCountBits(int $version): int
    {
        return $version < 10 ? 8 : 16;
    }

    /**
     * Number of modules available for data and error correction codewords
     *
     * @access private
     * @param  integer $version
     * @return integer
     */
    private function getRawModuleCount(int $version): int
    {
        // Full grid minus finder patterns, separators, format information, dark module and timing patterns
        $count = (16 * $version + 128) * $version + 64;

        if ($version >= 2) {
            $alignments = intdiv($version, 7) + 2;
            // Alignment patterns, minus the modules they share with timing patterns
            $count -= (25 * $alignments - 10) * $alignments - 55;

            if ($version >= 7) {
                $count -= 36;
            }
        }

        return $count;
    }

    private function getDataCodewordCount(int $version): int
    {
        return intdiv($this->getRawModuleCount($version), 8) -
            self::ECC_CODEWORDS_PER_BLOCK[$this->level][$version] * self::ECC_BLOCKS[$this->level][$version];
    }

    /**
     * Build the data codewords: ECI header, mode indicator, character count, data, terminator and padding
     *
     * @access private
     * @param  string  $header
     * @param  string  $data
     * @param  integer $version
     * @return int[]
     */
    private function getDataCodewords(string $header, string $data, int $version): array
    {
        $capacity = $this->getDataCodewordCount($version) * 8;
        $bits = $header.'0100'.str_pad(decbin(strlen($data)), $this->getCharacterCountBits($version), '0', STR_PAD_LEFT);

        foreach (unpack('C*', $data) as $byte) {
            $bits .= str_pad(decbin($byte), 8, '0', STR_PAD_LEFT);
        }

        $bits .= str_repeat('0', min(4, $capacity - strlen($bits)));
        $bits .= str_repeat('0', (8 - strlen($bits) % 8) % 8);

        $codewords = array_map('bindec', str_split($bits, 8));

        for ($pad = 0xEC; count($codewords) < $capacity / 8; $pad ^= 0xEC ^ 0x11) {
            $codewords[] = $pad;
        }

        return $codewords;
    }

    /**
     * Split data codewords into blocks, compute error correction and interleave everything
     *
     * @access private
     * @param  int[]   $data
     * @param  integer $version
     * @return int[]
     */
    private function addErrorCorrection(array $data, int $version): array
    {
        $blockCount = self::ECC_BLOCKS[$this->level][$version];
        $eccLength = self::ECC_CODEWORDS_PER_BLOCK[$this->level][$version];
        $shortBlockLength = intdiv(count($data), $blockCount);
        $longBlockCount = count($data) % $blockCount;
        $dataBlocks = [];
        $eccBlocks = [];
        $offset = 0;

        for ($i = 0; $i < $blockCount; $i++) {
            $length = $shortBlockLength + ($i >= $blockCount - $longBlockCount ? 1 : 0);
            $block = array_slice($data, $offset, $length);
            $offset += $length;

            $dataBlocks[] = $block;
            $eccBlocks[] = $this->computeErrorCorrection($block, $eccLength);
        }

        $result = [];

        for ($i = 0; $i <= $shortBlockLength; $i++) {
            foreach ($dataBlocks as $block) {
                if (isset($block[$i])) {
                    $result[] = $block[$i];
                }
            }
        }

        for ($i = 0; $i < $eccLength; $i++) {
            foreach ($eccBlocks as $block) {
                $result[] = $block[$i];
            }
        }

        return $result;
    }

    /**
     * Reed-Solomon error correction codewords over GF(256) with polynomial x^8 + x^4 + x^3 + x^2 + 1
     *
     * @access private
     * @param  int[]   $data
     * @param  integer $degree
     * @return int[]
     */
    private function computeErrorCorrection(array $data, int $degree): array
    {
        if (empty(self::$gfExp)) {
            for ($i = 0, $value = 1; $i < 255; $i++) {
                self::$gfExp[$i] = $value;
                self::$gfLog[$value] = $i;
                $value <<= 1;

                if ($value & 0x100) {
                    $value ^= 0x11D;
                }
            }
        }

        // Generator polynomial (x - a^0)(x - a^1)...(x - a^(degree - 1)), highest degree first
        $generator = [1];

        for ($i = 0; $i < $degree; $i++) {
            $next = array_fill(0, count($generator) + 1, 0);

            foreach ($generator as $j => $coefficient) {
                $next[$j] ^= $coefficient;
                $next[$j + 1] ^= $this->gfMultiply($coefficient, self::$gfExp[$i]);
            }

            $generator = $next;
        }

        $remainder = array_fill(0, $degree, 0);

        foreach ($data as $byte) {
            $factor = $byte ^ array_shift($remainder);
            $remainder[] = 0;

            for ($i = 0; $i < $degree; $i++) {
                $remainder[$i] ^= $this->gfMultiply($generator[$i + 1], $factor);
            }
        }

        return $remainder;
    }

    private function gfMultiply(int $a, int $b): int
    {
        if ($a === 0 || $b === 0) {
            return 0;
        }

        return self::$gfExp[(self::$gfLog[$a] + self::$gfLog[$b]) % 255];
    }

    /**
     * Draw finder, timing and alignment patterns, and reserve format and version areas
     *
     * @access private
     * @param  integer $version
     */
    private function drawFunctionPatterns(int $version): void
    {
        for ($i = 0; $i < $this->size; $i++) {
            $this->setFunctionModule($i, 6, $i % 2 === 0);
            $this->setFunctionModule(6, $i, $i % 2 === 0);
        }

        $this->drawFinderPattern(3, 3);
        $this->drawFinderPattern($this->size - 4, 3);
        $this->drawFinderPattern(3, $this->size - 4);

        $positions = $this->getAlignmentPositions($version);
        $last = count($positions) - 1;

        foreach ($positions as $i => $x) {
            foreach ($positions as $j => $y) {
                // Skip the three corners occupied by finder patterns
                if (($i === 0 && $j === 0) || ($i === 0 && $j === $last) || ($i === $last && $j === 0)) {
                    continue;
                }

                for ($dy = -2; $dy <= 2; $dy++) {
                    for ($dx = -2; $dx <= 2; $dx++) {
                        $this->setFunctionModule($x + $dx, $y + $dy, max(abs($dx), abs($dy)) !== 1);
                    }
                }
            }
        }

        $this->drawFormatBits(0);
        $this->drawVersionBits($version);
    }

    /**
     * Draw a finder pattern and its separator around the given center
     *
     * @access private
     * @param  integer $x
     * @param  integer $y
     */
    private function drawFinderPattern(int $x, int $y): void
    {
        for ($dy = -4; $dy <= 4; $dy++) {
            for ($dx = -4; $dx <= 4; $dx++) {
                $distance = max(abs($dx), abs($dy));

                if ($x + $dx >= 0 && $x + $dx < $this->size && $y + $dy >= 0 && $y + $dy < $this->size) {
                    $this->setFunctionModule($x + $dx, $y + $dy, $distance !== 2 && $distance !== 4);
                }
            }
        }
    }

    /**
     * Get alignment pattern center coordinates (same list for rows and columns)
     *
     * @access private
     * @param  integer $version
     * @return int[]
     */
    private function getAlignmentPositions(int $version): array
    {
        if ($version === 1) {
            return [];
        }

        $count = intdiv($version, 7) + 2;
        $step = $version === 32 ? 26 : (int) ceil(($version * 4 + 4) / ($count * 2 - 2)) * 2;
        $positions = [6];

        for ($i = $count - 2; $i >= 0; $i--) {
            $positions[] = $this->size - 7 - $i * $step;
        }

        return $positions;
    }

    /**
     * Draw both copies of the format information and the dark module
     *
     * @access private
     * @param  integer $mask
     */
    private function drawFormatBits(int $mask): void
    {
        $data = self::FORMAT_LEVEL_BITS[$this->level] << 3 | $mask;
        $bits = ($data << 10 | $this->bchRemainder($data, 0x537, 10)) ^ 0x5412;

        for ($i = 0; $i < 15; $i++) {
            $bit = ($bits >> $i & 1) === 1;

            // Around the top-left finder pattern, skipping the vertical timing pattern
            if ($i < 6) {
                $this->setFunctionModule(8, $i, $bit);
            } elseif ($i < 8) {
                $this->setFunctionModule(8, $i + 1, $bit);
            } elseif ($i === 8) {
                $this->setFunctionModule(7, 8, $bit);
            } else {
                $this->setFunctionModule(14 - $i, 8, $bit);
            }

            // Split between the top-right and bottom-left finder patterns
            if ($i < 8) {
                $this->setFunctionModule($this->size - 1 - $i, 8, $bit);
            } else {
                $this->setFunctionModule(8, $this->size - 15 + $i, $bit);
            }
        }

        $this->setFunctionModule(8, $this->size - 8, true);
    }

    /**
     * Draw both copies of the version information (version 7 and above)
     *
     * @access private
     * @param  integer $version
     */
    private function drawVersionBits(int $version): void
    {
        if ($version < 7) {
            return;
        }

        $bits = $version << 12 | $this->bchRemainder($version, 0x1F25, 12);

        for ($i = 0; $i < 18; $i++) {
            $bit = ($bits >> $i & 1) === 1;
            $a = $this->size - 11 + $i % 3;
            $b = intdiv($i, 3);

            $this->setFunctionModule($a, $b, $bit);
            $this->setFunctionModule($b, $a, $bit);
        }
    }

    private function bchRemainder(int $data, int $generator, int $degree): int
    {
        $remainder = $data << $degree;

        for ($bit = 31; $bit >= $degree; $bit--) {
            if ($remainder >> $bit & 1) {
                $remainder ^= $generator << ($bit - $degree);
            }
        }

        return $remainder;
    }

    private function setFunctionModule(int $x, int $y, bool $dark): void
    {
        $this->modules[$y][$x] = $dark;
        $this->reserved[$y][$x] = true;
    }

    /**
     * Place codewords in the two-module wide zigzag, from the bottom-right corner
     *
     * @access private
     * @param  int[] $codewords
     */
    private function drawCodewords(array $codewords): void
    {
        $bitCount = count($codewords) * 8;
        $index = 0;

        for ($right = $this->size - 1; $right >= 1; $right -= 2) {
            if ($right === 6) {
                $right = 5;
            }

            $upward = (($right + 1) & 2) === 0;

            for ($vertical = 0; $vertical < $this->size; $vertical++) {
                $y = $upward ? $this->size - 1 - $vertical : $vertical;

                for ($j = 0; $j < 2; $j++) {
                    $x = $right - $j;

                    if (! $this->reserved[$y][$x] && $index < $bitCount) {
                        $this->modules[$y][$x] = ($codewords[$index >> 3] >> (7 - ($index & 7)) & 1) === 1;
                        $index++;
                    }
                }
            }
        }
    }

    /**
     * Apply the mask pattern with the lowest penalty score
     *
     * @access private
     */
    private function applyBestMask(): void
    {
        $unmasked = $this->modules;
        $bestScore = PHP_INT_MAX;
        $bestModules = [];

        for ($mask = 0; $mask < 8; $mask++) {
            $this->modules = $unmasked;
            $this->applyMask($mask);
            $this->drawFormatBits($mask);
            $score = $this->getPenaltyScore();

            if ($score < $bestScore) {
                $bestScore = $score;
                $bestModules = $this->modules;
            }
        }

        $this->modules = $bestModules;
    }

    private function applyMask(int $mask): void
    {
        for ($y = 0; $y < $this->size; $y++) {
            for ($x = 0; $x < $this->size; $x++) {
                if ($this->reserved[$y][$x]) {
                    continue;
                }

                $invert = match ($mask) {
                    0 => ($y + $x) % 2 === 0,
                    1 => $y % 2 === 0,
                    2 => $x % 3 === 0,
                    3 => ($y + $x) % 3 === 0,
                    4 => (intdiv($y, 2) + intdiv($x, 3)) % 2 === 0,
                    5 => ($y * $x) % 2 + ($y * $x) % 3 === 0,
                    6 => (($y * $x) % 2 + ($y * $x) % 3) % 2 === 0,
                    7 => (($y + $x) % 2 + ($y * $x) % 3) % 2 === 0,
                };

                if ($invert) {
                    $this->modules[$y][$x] = ! $this->modules[$y][$x];
                }
            }
        }
    }

    /**
     * Compute the penalty score of the current symbol
     *
     * @access private
     * @return integer
     */
    private function getPenaltyScore(): int
    {
        $score = 0;
        $dark = 0;
        $lines = [];

        for ($i = 0; $i < $this->size; $i++) {
            $row = '';
            $column = '';

            for ($j = 0; $j < $this->size; $j++) {
                $row .= $this->modules[$i][$j] ? '1' : '0';
                $column .= $this->modules[$j][$i] ? '1' : '0';
            }

            $dark += substr_count($row, '1');
            $lines[] = $row;
            $lines[] = $column;
        }

        foreach ($lines as $line) {
            // Runs of five or more modules of the same color
            preg_match_all('/0{5,}|1{5,}/', $line, $matches);

            foreach ($matches[0] as $run) {
                $score += self::PENALTY_N1 + strlen($run) - 5;
            }

            // Finder-like 1:1:3:1:1 patterns next to four light modules (the quiet zone counts as light)
            $line = '0000'.$line.'0000';
            $score += self::PENALTY_N3 * (substr_count($line, '10111010000') + substr_count($line, '00001011101'));
        }

        // 2x2 blocks of the same color
        for ($y = 0; $y < $this->size - 1; $y++) {
            for ($x = 0; $x < $this->size - 1; $x++) {
                $color = $this->modules[$y][$x];

                if ($color === $this->modules[$y][$x + 1] && $color === $this->modules[$y + 1][$x] && $color === $this->modules[$y + 1][$x + 1]) {
                    $score += self::PENALTY_N2;
                }
            }
        }

        // Deviation of the dark module ratio from 50%, in steps of 5%
        $total = $this->size * $this->size;
        $score += self::PENALTY_N4 * intdiv(abs($dark * 100 - $total * 50), $total * 5);

        return $score;
    }
}
