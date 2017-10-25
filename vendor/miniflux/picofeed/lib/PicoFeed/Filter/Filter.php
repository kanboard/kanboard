<?php

namespace PicoFeed\Filter;

/**
 * Filter class.
 *
 * @author  Frederic Guillot
 */
class Filter
{
    /**
     * Get the Html filter instance.
     *
     * @static
     *
     * @param string $html    HTML content
     * @param string $website Site URL (used to build absolute URL)
     *
     * @return Html
     */
    public static function html($html, $website)
    {
        $filter = new Html($html, $website);

        return $filter;
    }

    /**
     * Escape HTML content.
     *
     * @static
     *
     * @return string
     */
    public static function escape($content)
    {
        return htmlspecialchars($content, ENT_QUOTES, 'UTF-8', false);
    }

    /**
     * Remove HTML tags.
     *
     * @param string $data Input data
     *
     * @return string
     */
    public function removeHTMLTags($data)
    {
        return preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $data);
    }

    /**
     * Remove the XML tag from a document.
     *
     * @static
     *
     * @param string $data Input data
     *
     * @return string
     */
    public static function stripXmlTag($data)
    {
        if (strpos($data, '<?xml') !== false) {
            $data = ltrim(substr($data, strpos($data, '?>') + 2));
        }

        do {
            $pos = strpos($data, '<?xml-stylesheet ');

            if ($pos !== false) {
                $data = ltrim(substr($data, strpos($data, '?>') + 2));
            }
        } while ($pos !== false && $pos < 200);

        return $data;
    }

    /**
     * Strip head tag from the HTML content.
     *
     * @static
     *
     * @param string $data Input data
     *
     * @return string
     */
    public static function stripHeadTags($data)
    {
        return preg_replace('@<head[^>]*?>.*?</head>@siu', '', $data);
    }

    /**
     * Trim whitespace from the begining, the end and inside a string and don't break utf-8 string.
     *
     * @static
     *
     * @param string $value Raw data
     *
     * @return string Normalized data
     */
    public static function stripWhiteSpace($value)
    {
        $value = str_replace("\r", ' ', $value);
        $value = str_replace("\t", ' ', $value);
        $value = str_replace("\n", ' ', $value);
        // $value = preg_replace('/\s+/', ' ', $value); <= break utf-8
        return trim($value);
    }

    /**
     * Fixes before XML parsing.
     *
     * @static
     *
     * @param string $data Raw data
     *
     * @return string Normalized data
     */
    public static function normalizeData($data)
    {
        $entities = array(
            '/(&#)(\d+);/m', // decimal encoded
            '/(&#x)([a-f0-9]+);/mi', // hex encoded
        );

        // strip invalid XML 1.0 characters which are encoded as entities
        $data = preg_replace_callback($entities, function ($matches) {
            $code_point = $matches[2];

            // convert hex entity to decimal
            if (strtolower($matches[1]) === '&#x') {
                $code_point = hexdec($code_point);
            }

            $code_point = (int) $code_point;

            // replace invalid characters
            if ($code_point < 9
                || ($code_point > 10 && $code_point < 13)
                || ($code_point > 13 && $code_point < 32)
                || ($code_point > 55295 && $code_point < 57344)
                || ($code_point > 65533 && $code_point < 65536)
                || $code_point > 1114111
            ) {
                return '';
            };

            return $matches[0];
        }, $data);

        // strip every utf-8 character than isn't in the range of valid XML 1.0 characters
        return (string) preg_replace('/[^\x{0009}\x{000A}\x{000D}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]/u', '', $data);
    }
}
