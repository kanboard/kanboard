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

namespace PhpOffice\PhpWord\ComplexType;

/**
 * Visibility of Annotation Types.
 *
 * @see http://www.datypic.com/sc/ooxml/e-w_revisionView-1.html
 */
final class TrackChangesView
{
    /**
     * Display Visual Indicator Of Markup Area.
     *
     * @var bool
     */
    private $markup;

    /**
     * Display Comments.
     *
     * @var bool
     */
    private $comments;

    /**
     * Display Content Revisions.
     *
     * @var bool
     */
    private $insDel;

    /**
     * Display Formatting Revisions.
     *
     * @var bool
     */
    private $formatting;

    /**
     * Display Ink Annotations.
     *
     * @var bool
     */
    private $inkAnnotations;

    /**
     * Get Display Visual Indicator Of Markup Area.
     *
     * @return bool True if markup is shown
     */
    public function hasMarkup()
    {
        return $this->markup;
    }

    /**
     * Set Display Visual Indicator Of Markup Area.
     *
     * @param ?bool $markup
     *            Set to true to show markup
     */
    public function setMarkup($markup): void
    {
        $this->markup = $markup === null ? true : $markup;
    }

    /**
     * Get Display Comments.
     *
     * @return bool True if comments are shown
     */
    public function hasComments()
    {
        return $this->comments;
    }

    /**
     * Set Display Comments.
     *
     * @param ?bool $comments
     *            Set to true to show comments
     */
    public function setComments($comments): void
    {
        $this->comments = $comments === null ? true : $comments;
    }

    /**
     * Get Display Content Revisions.
     *
     * @return bool True if content revisions are shown
     */
    public function hasInsDel()
    {
        return $this->insDel;
    }

    /**
     * Set Display Content Revisions.
     *
     * @param ?bool $insDel
     *            Set to true to show content revisions
     */
    public function setInsDel($insDel): void
    {
        $this->insDel = $insDel === null ? true : $insDel;
    }

    /**
     * Get Display Formatting Revisions.
     *
     * @return bool True if formatting revisions are shown
     */
    public function hasFormatting()
    {
        return $this->formatting;
    }

    /**
     * Set Display Formatting Revisions.
     *
     * @param null|bool $formatting
     *            Set to true to show formatting revisions
     */
    public function setFormatting($formatting = null): void
    {
        $this->formatting = $formatting === null ? true : $formatting;
    }

    /**
     * Get Display Ink Annotations.
     *
     * @return bool True if ink annotations are shown
     */
    public function hasInkAnnotations()
    {
        return $this->inkAnnotations;
    }

    /**
     * Set Display Ink Annotations.
     *
     * @param ?bool $inkAnnotations
     *            Set to true to show ink annotations
     */
    public function setInkAnnotations($inkAnnotations): void
    {
        $this->inkAnnotations = $inkAnnotations === null ? true : $inkAnnotations;
    }
}
