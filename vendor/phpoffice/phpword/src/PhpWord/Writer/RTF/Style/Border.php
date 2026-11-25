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

namespace PhpOffice\PhpWord\Writer\RTF\Style;

/**
 * Border style writer.
 *
 * @since 0.12.0
 */
class Border extends AbstractStyle
{
    /**
     * Sizes.
     *
     * @var array
     */
    private $sizes = [];

    /**
     * Colors.
     *
     * @var array
     */
    private $colors = [];

    /**
     * Write style.
     *
     * @return string
     */
    public function write()
    {
        $content = '';

        $sides = ['top', 'left', 'right', 'bottom'];
        $sizeCount = count($this->sizes);

        // Page border measure
        // 8 = from text, infront off; 32 = from edge, infront on; 40 = from edge, infront off
        $content .= '\pgbrdropt32';

        for ($i = 0; $i < $sizeCount; ++$i) {
            if ($this->sizes[$i] !== null) {
                $color = null;
                if (isset($this->colors[$i])) {
                    $color = $this->colors[$i];
                }
                $content .= $this->writeSide($sides[$i], $this->sizes[$i], $color);
            }
        }

        return $content;
    }

    /**
     * Write side.
     *
     * @param string $side
     * @param int $width
     * @param string $color
     *
     * @return string
     */
    private function writeSide($side, $width, $color = '')
    {
        /** @var \PhpOffice\PhpWord\Writer\RTF $rtfWriter */
        $rtfWriter = $this->getParentWriter();
        $colorIndex = 0;
        if ($rtfWriter !== null) {
            $colorTable = $rtfWriter->getColorTable();
            $index = array_search($color, $colorTable);
            if ($index !== false && $colorIndex !== null) {
                $colorIndex = $index + 1;
            }
        }

        $content = '';

        $content .= '\pgbrdr' . substr($side, 0, 1);
        $content .= '\brdrs'; // Single-thickness border; @todo Get other type of border
        $content .= '\brdrw' . round($width); // Width
        $content .= '\brdrcf' . $colorIndex; // Color
        $content .= '\brsp480'; // Space in twips between borders and the paragraph (24pt, following OOXML)
        $content .= ' ';

        return $content;
    }

    /**
     * Set sizes.
     *
     * @param int[] $value
     */
    public function setSizes($value): void
    {
        $this->sizes = $value;
    }

    /**
     * Set colors.
     *
     * @param string[] $value
     */
    public function setColors($value): void
    {
        $this->colors = $value;
    }
}
