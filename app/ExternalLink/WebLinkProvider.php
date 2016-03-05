<?php

namespace Kanboard\ExternalLink;

use Kanboard\Core\ExternalLink\ExternalLinkProviderInterface;

/**
 * Web Link Provider
 *
 * @package  externalLink
 * @author   Frederic Guillot
 */
class WebLinkProvider extends BaseLinkProvider implements ExternalLinkProviderInterface
{
    /**
     * Get provider name
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return t('Web Link');
    }

    /**
     * Get link type
     *
     * @access public
     * @return string
     */
    public function getType()
    {
        return 'weblink';
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
        $startWithHttp = strpos($this->userInput, 'http://') === 0 || strpos($this->userInput, 'https://') === 0;
        $validUrl = filter_var($this->userInput, FILTER_VALIDATE_URL);

        return $startWithHttp && $validUrl;
    }

    /**
     * Get the link found with the properties
     *
     * @access public
     * @return \Kanboard\Core\ExternalLink\ExternalLinkInterface
     */
    public function getLink()
    {
        $link = new WebLink($this->container);
        $link->setUrl($this->userInput);

        return $link;
    }
}
