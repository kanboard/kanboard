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

namespace PhpOffice\PhpWord\SimpleType;

use PhpOffice\PhpWord\Shared\AbstractEnum;

/**
 * Numbering Format.
 *
 * @since 0.14.0
 * @see http://www.datypic.com/sc/ooxml/t-w_ST_NumberFormat.html.
 */
final class NumberFormat extends AbstractEnum
{
    //Decimal Numbers
    const DECIMAL = 'decimal';
    //Uppercase Roman Numerals
    const UPPER_ROMAN = 'upperRoman';
    //Lowercase Roman Numerals
    const LOWER_ROMAN = 'lowerRoman';
    //Uppercase Latin Alphabet
    const UPPER_LETTER = 'upperLetter';
    //Lowercase Latin Alphabet
    const LOWER_LETTER = 'lowerLetter';
    //Ordinal
    const ORDINAL = 'ordinal';
    //Cardinal Text
    const CARDINAL_TEXT = 'cardinalText';
    //Ordinal Text
    const ORDINAL_TEXT = 'ordinalText';
    //Hexadecimal Numbering
    const HEX = 'hex';
    //Chicago Manual of Style
    const CHICAGO = 'chicago';
    //Ideographs
    const IDEOGRAPH_DIGITAL = 'ideographDigital';
    //Japanese Counting System
    const JAPANESE_COUNTING = 'japaneseCounting';
    //AIUEO Order Hiragana
    const AIUEO = 'aiueo';
    //Iroha Ordered Katakana
    const IROHA = 'iroha';
    //Double Byte Arabic Numerals
    const DECIMAL_FULL_WIDTH = 'decimalFullWidth';
    //Single Byte Arabic Numerals
    const DECIMAL_HALF_WIDTH = 'decimalHalfWidth';
    //Japanese Legal Numbering
    const JAPANESE_LEGAL = 'japaneseLegal';
    //Japanese Digital Ten Thousand Counting System
    const JAPANESE_DIGITAL_TEN_THOUSAND = 'japaneseDigitalTenThousand';
    //Decimal Numbers Enclosed in a Circle
    const DECIMAL_ENCLOSED_CIRCLE = 'decimalEnclosedCircle';
    //Double Byte Arabic Numerals Alternate
    const DECIMAL_FULL_WIDTH2 = 'decimalFullWidth2';
    //Full-Width AIUEO Order Hiragana
    const AIUEO_FULL_WIDTH = 'aiueoFullWidth';
    //Full-Width Iroha Ordered Katakana
    const IROHA_FULL_WIDTH = 'irohaFullWidth';
    //Initial Zero Arabic Numerals
    const DECIMAL_ZERO = 'decimalZero';
    //Bullet
    const BULLET = 'bullet';
    //Korean Ganada Numbering
    const GANADA = 'ganada';
    //Korean Chosung Numbering
    const CHOSUNG = 'chosung';
    //Decimal Numbers Followed by a Period
    const DECIMAL_ENCLOSED_FULL_STOP = 'decimalEnclosedFullstop';
    //Decimal Numbers Enclosed in Parenthesis
    const DECIMAL_ENCLOSED_PAREN = 'decimalEnclosedParen';
    //Decimal Numbers Enclosed in a Circle
    const DECIMAL_ENCLOSED_CIRCLE_CHINESE = 'decimalEnclosedCircleChinese';
    //Ideographs Enclosed in a Circle
    const IDEOGRAPHENCLOSEDCIRCLE = 'ideographEnclosedCircle';
    //Traditional Ideograph Format
    const IDEOGRAPH_TRADITIONAL = 'ideographTraditional';
    //Zodiac Ideograph Format
    const IDEOGRAPH_ZODIAC = 'ideographZodiac';
    //Traditional Zodiac Ideograph Format
    const IDEOGRAPH_ZODIAC_TRADITIONAL = 'ideographZodiacTraditional';
    //Taiwanese Counting System
    const TAIWANESE_COUNTING = 'taiwaneseCounting';
    //Traditional Legal Ideograph Format
    const IDEOGRAPH_LEGAL_TRADITIONAL = 'ideographLegalTraditional';
    //Taiwanese Counting Thousand System
    const TAIWANESE_COUNTING_THOUSAND = 'taiwaneseCountingThousand';
    //Taiwanese Digital Counting System
    const TAIWANESE_DIGITAL = 'taiwaneseDigital';
    //Chinese Counting System
    const CHINESE_COUNTING = 'chineseCounting';
    //Chinese Legal Simplified Format
    const CHINESE_LEGAL_SIMPLIFIED = 'chineseLegalSimplified';
    //Chinese Counting Thousand System
    const CHINESE_COUNTING_THOUSAND = 'chineseCountingThousand';
    //Korean Digital Counting System
    const KOREAN_DIGITAL = 'koreanDigital';
    //Korean Counting System
    const KOREAN_COUNTING = 'koreanCounting';
    //Korean Legal Numbering
    const KOREAN_LEGAL = 'koreanLegal';
    //Korean Digital Counting System Alternate
    const KOREAN_DIGITAL2 = 'koreanDigital2';
    //Vietnamese Numerals
    const VIETNAMESE_COUNTING = 'vietnameseCounting';
    //Lowercase Russian Alphabet
    const RUSSIAN_LOWER = 'russianLower';
    //Uppercase Russian Alphabet
    const RUSSIAN_UPPER = 'russianUpper';
    //No Numbering
    const NONE = 'none';
    //Number With Dashes
    const NUMBER_IN_DASH = 'numberInDash';
    //Hebrew Numerals
    const HEBREW1 = 'hebrew1';
    //Hebrew Alphabet
    const HEBREW2 = 'hebrew2';
    //Arabic Alphabet
    const ARABIC_ALPHA = 'arabicAlpha';
    //Arabic Abjad Numerals
    const ARABIC_ABJAD = 'arabicAbjad';
    //Hindi Vowels
    const HINDI_VOWELS = 'hindiVowels';
    //Hindi Consonants
    const HINDI_CONSONANTS = 'hindiConsonants';
    //Hindi Numbers
    const HINDI_NUMBERS = 'hindiNumbers';
    //Hindi Counting System
    const HINDI_COUNTING = 'hindiCounting';
    //Thai Letters
    const THAI_LETTERS = 'thaiLetters';
    //Thai Numerals
    const THAI_NUMBERS = 'thaiNumbers';
    //Thai Counting System
    const THAI_COUNTING = 'thaiCounting';
}
