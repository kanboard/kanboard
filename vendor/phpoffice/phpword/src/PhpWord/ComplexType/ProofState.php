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

use InvalidArgumentException;

/**
 * Spelling and Grammatical Checking State.
 *
 * @see http://www.datypic.com/sc/ooxml/e-w_proofState-1.html
 */
final class ProofState
{
    /**
     * Check Completed.
     */
    const CLEAN = 'clean';

    /**
     * Check Not Completed.
     */
    const DIRTY = 'dirty';

    /**
     * Spell Checking State.
     *
     * @var string
     */
    private $spelling;

    /**
     * Grammatical Checking State.
     *
     * @var string
     */
    private $grammar;

    /**
     * Set the Spell Checking State (dirty or clean).
     *
     * @param string $spelling
     *
     * @return self
     */
    public function setSpelling($spelling)
    {
        if ($spelling == self::CLEAN || $spelling == self::DIRTY) {
            $this->spelling = $spelling;
        } else {
            throw new InvalidArgumentException('Invalid value, dirty or clean possible');
        }

        return $this;
    }

    /**
     * Get the Spell Checking State.
     *
     * @return string
     */
    public function getSpelling()
    {
        return $this->spelling;
    }

    /**
     * Set the Grammatical Checking State (dirty or clean).
     *
     * @param string $grammar
     *
     * @return self
     */
    public function setGrammar($grammar)
    {
        if ($grammar == self::CLEAN || $grammar == self::DIRTY) {
            $this->grammar = $grammar;
        } else {
            throw new InvalidArgumentException('Invalid value, dirty or clean possible');
        }

        return $this;
    }

    /**
     * Get the Grammatical Checking State.
     *
     * @return string
     */
    public function getGrammar()
    {
        return $this->grammar;
    }
}
