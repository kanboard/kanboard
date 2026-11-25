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
declare(strict_types=1);

namespace PhpOffice\PhpWord\Shared;

class Css
{
    /**
     * @var string
     */
    private $cssContent;

    /**
     * @var array<string, array<string, string>>
     */
    private $styles = [];

    public function __construct(string $cssContent)
    {
        $this->cssContent = $cssContent;
    }

    public function process(): void
    {
        $cssContent = str_replace(["\r", "\n"], '', $this->cssContent);
        preg_match_all('/(.+?)\s?\{\s?(.+?)\s?\}/', $cssContent, $cssExtracted);
        // Check if there are x selectors and x rules
        if (count($cssExtracted[1]) != count($cssExtracted[2])) {
            return;
        }

        foreach ($cssExtracted[1] as $key => $selector) {
            $rules = trim($cssExtracted[2][$key]);
            $rules = explode(';', $rules);
            foreach ($rules as $rule) {
                if (empty($rule)) {
                    continue;
                }
                [$key, $value] = explode(':', trim($rule));
                $this->styles[$this->sanitize($selector)][$this->sanitize($key)] = $this->sanitize($value);
            }
        }
    }

    public function getStyles(): array
    {
        return $this->styles;
    }

    public function getStyle(string $selector): array
    {
        $selector = $this->sanitize($selector);

        return $this->styles[$selector] ?? [];
    }

    private function sanitize(string $value): string
    {
        return addslashes(trim($value));
    }
}
