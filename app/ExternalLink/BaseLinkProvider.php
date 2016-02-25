<?php

namespace Kanboard\ExternalLink;

use Kanboard\Core\Base;

/**
 * Base Link Provider
 *
 * @package  externalLink
 * @author   Frederic Guillot
 */
abstract class BaseLinkProvider extends Base
{
    /**
     * User input
     *
     * @access protected
     * @var string
     */
    protected $userInput = '';

    /**
     * Set text entered by the user
     *
     * @access public
     * @param  string $input
     */
    public function setUserTextInput($input)
    {
        $this->userInput = trim($input);
    }
}
