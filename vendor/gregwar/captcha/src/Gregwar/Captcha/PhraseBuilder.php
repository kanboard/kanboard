<?php

namespace Gregwar\Captcha;

/**
 * Generates random phrase
 *
 * @author Gregwar <g.passault@gmail.com>
 */
class PhraseBuilder implements PhraseBuilderInterface
{
    /**
     * @var int
     */
    public $length;

    /**
     * @var string
     */
    public $charset;
    /**
     * Constructs a PhraseBuilder with given parameters
     */
    public function __construct($length = 5, $charset = 'abcdefghijklmnpqrstuvwxyz123456789')
    {
        $this->length = $length;
        $this->charset = $charset;
    }

    /**
     * Generates  random phrase of given length with given charset
     */
    public function build($length = null, $charset = null)
    {
        if ($length !== null) {
            $this->length = $length;
        }
        if ($charset !== null) {
            $this->charset = $charset;
        }

        $phrase = '';
        $chars = str_split($this->charset);

        for ($i = 0; $i < $this->length; $i++) {
            $phrase .= $chars[array_rand($chars)];
        }

        return $phrase;
    }

    /**
     * "Niceize" a code
     */
    public function niceize($str)
    {
        return strtr(strtolower($str), '01', 'ol');
    }
}
