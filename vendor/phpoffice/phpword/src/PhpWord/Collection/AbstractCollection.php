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

namespace PhpOffice\PhpWord\Collection;

/**
 * Collection abstract class.
 *
 * @since 0.10.0
 *
 * @template T
 */
abstract class AbstractCollection
{
    /**
     * Items.
     *
     * @var T[]
     */
    private $items = [];

    /**
     * Get items.
     *
     * @return T[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Get item by index.
     *
     * @return ?T
     */
    public function getItem(int $index)
    {
        if (array_key_exists($index, $this->items)) {
            return $this->items[$index];
        }

        return null;
    }

    /**
     * Set item.
     *
     * @param ?T $item
     */
    public function setItem(int $index, $item): void
    {
        if (array_key_exists($index, $this->items)) {
            $this->items[$index] = $item;
        }
    }

    /**
     * Add new item.
     *
     * @param T $item
     */
    public function addItem($item): int
    {
        $index = $this->countItems();
        $this->items[$index] = $item;

        return $index;
    }

    /**
     * Get item count.
     */
    public function countItems(): int
    {
        return count($this->items);
    }
}
