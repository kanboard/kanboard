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

namespace PhpOffice\PhpWord\Style;

/**
 * Paragraph indentation style.
 *
 * @see  http://www.schemacentral.com/sc/ooxml/t-w_CT_Ind.html
 * @since 0.10.0
 */
class Indentation extends AbstractStyle
{
    /**
     * Left indentation (twip).
     *
     * @var null|float
     */
    private $left = 0;

    /**
     * Right indentation (twip).
     *
     * @var null|float
     */
    private $right = 0;

    /**
     * Additional first line indentation (twip).
     *
     * @var null|float
     */
    private $firstLine = 0;

    /**
     * Additional first line chars indentation (twip).
     *
     * @var int
     */
    private $firstLineChars = 0;

    /**
     * Indentation removed from first line (twip).
     *
     * @var null|float
     */
    private $hanging = 0;

    /**
     * Create a new instance.
     *
     * @param array $style
     */
    public function __construct($style = [])
    {
        $this->setStyleByArray($style);
    }

    /**
     * Get left.
     */
    public function getLeft(): ?float
    {
        return $this->left;
    }

    /**
     * Set left.
     */
    public function setLeft(?float $value): self
    {
        $this->left = $this->setNumericVal($value);

        return $this;
    }

    /**
     * Get right.
     */
    public function getRight(): ?float
    {
        return $this->right;
    }

    /**
     * Set right.
     */
    public function setRight(?float $value): self
    {
        $this->right = $this->setNumericVal($value);

        return $this;
    }

    /**
     * Get first line.
     */
    public function getFirstLine(): ?float
    {
        return $this->firstLine;
    }

    /**
     * Set first line.
     */
    public function setFirstLine(?float $value): self
    {
        $this->firstLine = $this->setNumericVal($value);

        return $this;
    }

    /**
     * Get first line chars.
     */
    public function getFirstLineChars(): int
    {
        return $this->firstLineChars;
    }

    /**
     * Set first line chars.
     */
    public function setFirstLineChars(int $value): self
    {
        $this->firstLineChars = $this->setIntVal($value, $this->firstLineChars);

        return $this;
    }

    /**
     * Get hanging.
     */
    public function getHanging(): ?float
    {
        return $this->hanging;
    }

    /**
     * Set hanging.
     */
    public function setHanging(?float $value = null): self
    {
        $this->hanging = $this->setNumericVal($value);

        return $this;
    }
}
