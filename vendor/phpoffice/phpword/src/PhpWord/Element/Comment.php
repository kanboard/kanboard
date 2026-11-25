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

use DateTime;

/**
 * Comment element.
 *
 * @see http://datypic.com/sc/ooxml/t-w_CT_Comment.html
 */
class Comment extends TrackChange
{
    /**
     * Initials.
     *
     * @var string
     */
    private $initials;

    /**
     * The Element where this comment starts.
     *
     * @var AbstractElement
     */
    private $startElement;

    /**
     * The Element where this comment ends.
     *
     * @var AbstractElement
     */
    private $endElement;

    /**
     * Is part of collection.
     *
     * @var bool
     */
    protected $collectionRelation = true;

    /**
     * Create a new Comment Element.
     *
     * @param string $author
     * @param null|DateTime $date
     * @param string $initials
     */
    public function __construct($author, $date = null, $initials = null)
    {
        parent::__construct(null, $author, $date);
        $this->initials = $initials;
    }

    /**
     * Get Initials.
     *
     * @return string
     */
    public function getInitials()
    {
        return $this->initials;
    }

    /**
     * Sets the element where this comment starts.
     */
    public function setStartElement(AbstractElement $value): void
    {
        $this->startElement = $value;
        $value->setCommentRangeStart($this);
    }

    /**
     * Get the element where this comment starts.
     *
     * @return AbstractElement
     */
    public function getStartElement()
    {
        return $this->startElement;
    }

    /**
     * Sets the element where this comment ends.
     */
    public function setEndElement(AbstractElement $value): void
    {
        $this->endElement = $value;
        $value->setCommentRangeEnd($this);
    }

    /**
     * Get the element where this comment ends.
     *
     * @return AbstractElement
     */
    public function getEndElement()
    {
        return $this->endElement;
    }
}
