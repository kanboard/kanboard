<?php

namespace Kanboard\Core\ExternalLink;

/**
 * External Link Provider Interface
 *
 * @package  externalLink
 * @author   Frederic Guillot
 */
interface ExternalLinkProviderInterface
{
    /**
     * Get provider name (label)
     *
     * @access public
     * @return string
     */
    public function getName();

    /**
     * Get link type (will be saved in the database)
     *
     * @access public
     * @return string
     */
    public function getType();

    /**
     * Get a dictionary of supported dependency types by the provider
     *
     * Example:
     *
     * [
     *     'related' => 'Related',
     *     'child' => 'Child',
     *     'parent' => 'Parent',
     *     'self' => 'Self',
     * ]
     *
     * The dictionary key is saved in the database.
     *
     * @access public
     * @return array
     */
    public function getDependencies();

    /**
     * Set text entered by the user
     *
     * @access public
     * @param  string $input
     */
    public function setUserTextInput($input);

    /**
     * Return true if the provider can parse correctly the user input
     *
     * @access public
     * @return boolean
     */
    public function match();

    /**
     * Get the link found with the properties
     *
     * @access public
     * @return ExternalLinkInterface
     */
    public function getLink();
}
