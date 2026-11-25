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

namespace PhpOffice\PhpWord\Element;

use PhpOffice\PhpWord\Style\ListItem as ListItemStyle;

/**
 * List item element.
 */
class ListItemRun extends TextRun
{
    /**
     * @var string Container type
     */
    protected $container = 'ListItemRun';

    /**
     * ListItem Style.
     *
     * @var ?ListItemStyle
     */
    private $style;

    /**
     * ListItem Depth.
     *
     * @var int
     */
    private $depth;

    /**
     * Create a new ListItem.
     *
     * @param int $depth
     * @param null|array|string $listStyle
     * @param mixed $paragraphStyle
     */
    public function __construct($depth = 0, $listStyle = null, $paragraphStyle = null)
    {
        $this->depth = $depth;

        // Version >= 0.10.0 will pass numbering style name. Older version will use old method
        if (null !== $listStyle && is_string($listStyle)) {
            $this->style = new ListItemStyle($listStyle);
        } else {
            $this->style = $this->setNewStyle(new ListItemStyle(), $listStyle, true);
        }
        parent::__construct($paragraphStyle);
    }

    /**
     * Get ListItem style.
     *
     * @return ?ListItemStyle
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * Get ListItem depth.
     *
     * @return int
     */
    public function getDepth()
    {
        return $this->depth;
    }
}
