<?php

namespace Kanboard\ExternalLink;

use Kanboard\Core\ExternalLink\ExternalLinkProviderInterface;

/**
 * File Link Provider
 *
 * @package  externalLink
 * @author   Frederic Guillot
 */
class FileLinkProvider extends BaseLinkProvider implements ExternalLinkProviderInterface
{
    protected $excludedPrefixes= array(
        'http',
        'ftp',
    );

    /**
     * Get provider name
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return t('Local File');
    }

    /**
     * Get link type
     *
     * @access public
     * @return string
     */
    public function getType()
    {
        return 'file';
    }

    /**
     * Get a dictionary of supported dependency types by the provider
     *
     * @access public
     * @return array
     */
    public function getDependencies()
    {
        return array(
            'related' => t('Related'),
        );
    }

    /**
     * Return true if the provider can parse correctly the user input
     *
     * @access public
     * @return boolean
     */
    public function match()
    {
        if (strpos($this->userInput, '://') === false) {
            return false;
        }

        foreach ($this->excludedPrefixes as $prefix) {
            if (strpos($this->userInput, $prefix) === 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the link found with the properties
     *
     * @access public
     * @return \Kanboard\Core\ExternalLink\ExternalLinkInterface
     */
    public function getLink()
    {
        $link = new FileLink($this->container);
        $link->setUrl($this->userInput);

        return $link;
    }
}
