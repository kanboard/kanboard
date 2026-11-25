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
 * Numbering style.
 *
 * @see  http://www.schemacentral.com/sc/ooxml/e-w_numbering.html
 * @see  http://www.schemacentral.com/sc/ooxml/e-w_abstractNum-1.html
 * @see  http://www.schemacentral.com/sc/ooxml/e-w_num-1.html
 * @since 0.10.0
 */
class Numbering extends AbstractStyle
{
    /**
     * Numbering definition instance ID.
     *
     * @var int
     *
     * @see  http://www.schemacentral.com/sc/ooxml/e-w_num-1.html
     */
    private $numId;

    /**
     * Multilevel type singleLevel|multilevel|hybridMultilevel.
     *
     * @var string
     *
     * @see  http://www.schemacentral.com/sc/ooxml/a-w_val-67.html
     */
    private $type;

    /**
     * Numbering levels.
     *
     * @var NumberingLevel[]
     */
    private $levels = [];

    /**
     * Get Id.
     */
    public function getNumId(): ?int
    {
        return $this->numId;
    }

    /**
     * Set Id.
     */
    public function setNumId(int $value): self
    {
        $this->numId = $this->setIntVal($value, $this->numId);

        return $this;
    }

    /**
     * Get multilevel type.
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * Set multilevel type.
     */
    public function setType(string $value): self
    {
        $enum = ['singleLevel', 'multilevel', 'hybridMultilevel'];
        $this->type = $this->setEnumVal($value, $enum, $this->type);

        return $this;
    }

    /**
     * Get levels.
     *
     * @return NumberingLevel[]
     */
    public function getLevels(): array
    {
        return $this->levels;
    }

    /**
     * Set multilevel type.
     */
    public function setLevels(array $values): self
    {
        if (is_array($values)) {
            foreach ($values as $key => $value) {
                $numberingLevel = new NumberingLevel();
                if (is_array($value)) {
                    $numberingLevel->setStyleByArray($value);
                    $numberingLevel->setLevel($key);
                }
                $this->levels[$key] = $numberingLevel;
            }
        }

        return $this;
    }
}
