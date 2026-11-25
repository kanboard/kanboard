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
 * TrackChange element.
 *
 * @see http://datypic.com/sc/ooxml/t-w_CT_TrackChange.html
 * @see http://datypic.com/sc/ooxml/t-w_CT_RunTrackChange.html
 */
class TrackChange extends AbstractContainer
{
    const INSERTED = 'INSERTED';
    const DELETED = 'DELETED';

    /**
     * @var string Container type
     */
    protected $container = 'TrackChange';

    /**
     * The type of change, (insert or delete), not applicable for PhpOffice\PhpWord\Element\Comment.
     *
     * @var string
     */
    private $changeType;

    /**
     * Author.
     *
     * @var string
     */
    private $author;

    /**
     * Date.
     *
     * @var DateTime
     */
    private $date;

    /**
     * Create a new TrackChange Element.
     *
     * @param string $changeType
     * @param string $author
     * @param null|bool|DateTime|int $date
     */
    public function __construct($changeType = null, $author = null, $date = null)
    {
        $this->changeType = $changeType;
        $this->author = $author;
        if ($date !== null && $date !== false) {
            $this->date = ($date instanceof DateTime) ? $date : new DateTime('@' . $date);
        }
    }

    /**
     * Get TrackChange Author.
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Get TrackChange Date.
     *
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Get the Change type.
     *
     * @return string
     */
    public function getChangeType()
    {
        return $this->changeType;
    }
}
