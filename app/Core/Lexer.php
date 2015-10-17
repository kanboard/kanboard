<?php

namespace Kanboard\Core;

/**
 * Lexer
 *
 * @package  core
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
        "/^(assignee:)/"                                 => 'T_ASSIGNEE',
        "/^(color:)/"                                    => 'T_COLOR',
        "/^(due:)/"                                      => 'T_DUE',
        "/^(updated:)/"                                  => 'T_UPDATED',
        "/^(modified:)/"                                 => 'T_UPDATED',
        "/^(created:)/"                                  => 'T_CREATED',
        "/^(status:)/"                                   => 'T_STATUS',
        "/^(description:)/"                              => 'T_DESCRIPTION',
        "/^(category:)/"                                 => 'T_CATEGORY',
        "/^(column:)/"                                   => 'T_COLUMN',
        "/^(project:)/"                                  => 'T_PROJECT',
        "/^(swimlane:)/"                                 => 'T_SWIMLANE',
        "/^(ref:)/"                                      => 'T_REFERENCE',
        "/^(reference:)/"                                => 'T_REFERENCE',
        "/^(\s+)/"                                       => 'T_WHITESPACE',
        '/^([<=>]{0,2}[0-9]{4}-[0-9]{2}-[0-9]{2})/'      => 'T_DATE',
        '/^(yesterday|tomorrow|today)/'                  => 'T_DATE',
        '/^("(.*?)")/'                                   => 'T_STRING',
        "/^(\w+)/"                                       => 'T_STRING',
        "/^(#\d+)/"                                      => 'T_STRING',
    );

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

        while (isset($input[$this->offset])) {
            $result = $this->match(substr($input, $this->offset));

            if ($result === false) {
                return array();
            }

            $tokens[] = $result;
        }

        return $tokens;
    }

    /**
     * Find a token that match and move the offset
     *
     * @access public
     * @param  string  $string
     * @return array|boolean
     */
    public function match($string)
    {
        foreach ($this->tokenMap as $pattern => $name) {
            if (preg_match($pattern, $string, $matches)) {
                $this->offset += strlen($matches[1]);

                return array(
                    'match' => trim($matches[1], '"'),
                    'token' => $name,
                );
            }
        }

        return false;
    }

    /**
     * Change the output of tokenizer to be easily parsed by the database filter
     *
     * Example: ['T_ASSIGNEE' => ['user1', 'user2'], 'T_TITLE' => 'task title']
     *
     * @access public
     * @param  array  $tokens
     * @return array
     */
    public function map(array $tokens)
    {
        $map = array(
            'T_TITLE' => '',
        );

        while (false !== ($token = current($tokens))) {
            switch ($token['token']) {
                case 'T_ASSIGNEE':
                case 'T_COLOR':
                case 'T_CATEGORY':
                case 'T_COLUMN':
                case 'T_PROJECT':
                case 'T_SWIMLANE':
                    $next = next($tokens);

                    if ($next !== false && $next['token'] === 'T_STRING') {
                        $map[$token['token']][] = $next['match'];
                    }

                    break;

                case 'T_STATUS':
                case 'T_DUE':
                case 'T_UPDATED':
                case 'T_CREATED':
                case 'T_DESCRIPTION':
                case 'T_REFERENCE':
                    $next = next($tokens);

                    if ($next !== false && ($next['token'] === 'T_DATE' || $next['token'] === 'T_STRING')) {
                        $map[$token['token']] = $next['match'];
                    }

                    break;

                default:
                    $map['T_TITLE'] .= $token['match'];
                    break;
            }

            next($tokens);
        }

        $map['T_TITLE'] = trim($map['T_TITLE']);

        if (empty($map['T_TITLE'])) {
            unset($map['T_TITLE']);
        }

        return $map;
    }
}
