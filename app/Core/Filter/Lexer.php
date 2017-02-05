<?php

namespace Kanboard\Core\Filter;

/**
 * Lexer
 *
 * @package  filter
 * @author   Frederic Guillot
 */
class Lexer
{
    /**
     * Current position
     *
     * @access private
     * @var integer
     */
    private $offset = 0;

    /**
     * Token map
     *
     * @access private
     * @var array
     */
    private $tokenMap = array(
        '/^(\s+)/'                                       => 'T_WHITESPACE',
        '/^([<=>]{0,2}[0-9]{4}-[0-9]{2}-[0-9]{2})/'      => 'T_STRING',
        '/^([<=>]{1,2}\w+)/u'                            => 'T_STRING',
        '/^([<=>]{1,2}".+")/'                            => 'T_STRING',
        '/^("(.*?)")/'                                   => 'T_STRING',
        '/^(\S+)/u'                                      => 'T_STRING',
        '/^(#\d+)/'                                      => 'T_STRING',
    );

    /**
     * Default token
     *
     * @access private
     * @var string
     */
    private $defaultToken = '';

    /**
     * Add token
     *
     * @access public
     * @param  string $regex
     * @param  string $token
     * @return $this
     */
    public function addToken($regex, $token)
    {
        $this->tokenMap = array($regex => $token) + $this->tokenMap;
        return $this;
    }

    /**
     * Set default token
     *
     * @access public
     * @param  string $token
     * @return $this
     */
    public function setDefaultToken($token)
    {
        $this->defaultToken = $token;
        return $this;
    }

    /**
     * Tokenize input string
     *
     * @access public
     * @param  string  $input
     * @return array
     */
    public function tokenize($input)
    {
        $tokens = array();
        $this->offset = 0;
        $input_length = mb_strlen($input, 'UTF-8');

        while ($this->offset < $input_length) {
            $result = $this->match(mb_substr($input, $this->offset, $input_length, 'UTF-8'));

            if ($result === false) {
                return array();
            }

            $tokens[] = $result;
        }

        return $this->map($tokens);
    }

    /**
     * Find a token that match and move the offset
     *
     * @access protected
     * @param  string  $string
     * @return array|boolean
     */
    protected function match($string)
    {
        foreach ($this->tokenMap as $pattern => $name) {
            if (preg_match($pattern, $string, $matches)) {
                $this->offset += mb_strlen($matches[1], 'UTF-8');

                return array(
                    'match' => str_replace('"', '', $matches[1]),
                    'token' => $name,
                );
            }
        }

        return false;
    }

    /**
     * Build map of tokens and matches
     *
     * @access protected
     * @param  array  $tokens
     * @return array
     */
    protected function map(array $tokens)
    {
        $map = array();
        $leftOver = '';

        while (false !== ($token = current($tokens))) {
            if ($token['token'] === 'T_STRING' || $token['token'] === 'T_WHITESPACE') {
                $leftOver .= $token['match'];
            } else {
                $next = next($tokens);

                if ($next !== false && $next['token'] === 'T_STRING') {
                    $map[$token['token']][] = $next['match'];
                }
            }

            next($tokens);
        }

        $leftOver = trim($leftOver);

        if ($this->defaultToken !== '' && $leftOver !== '') {
            $map[$this->defaultToken] = array($leftOver);
        }

        return $map;
    }
}
