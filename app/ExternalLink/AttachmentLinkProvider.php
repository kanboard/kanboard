<?php

namespace Kanboard\ExternalLink;

use Kanboard\Core\ExternalLink\ExternalLinkProviderInterface;

/**
 * Attachment Link Provider
 *
 * @package  externalLink
 * @author   Frederic Guillot
 */
class AttachmentLinkProvider extends BaseLinkProvider implements ExternalLinkProviderInterface
{
    /**
     * File extensions that are not attachments
     *
     * @access protected
     * @var array
     */
    protected $extensions = array(
        'html',
        'htm',
        'xhtml',
        'php',
        'jsp',
        'do',
        'action',
        'asp',
        'aspx',
        'cgi',
    );

    /**
     * Get provider name
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return t('Attachment');
    }

    /**
     * Get link type
     *
     * @access public
     * @return string
     */
    public function getType()
    {
        return 'attachment';
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
        if (preg_match('/^https?:\/\/.*\/.*\.([^\/]+)$/', $this->userInput, $matches)) {
            return $this->isValidExtension($matches[1]);
        }

        return false;
    }

    /**
     * Get the link found with the properties
     *
     * @access public
     * @return \Kanboard\Core\ExternalLink\ExternalLinkInterface
     */
    public function getLink()
    {
        $link = new AttachmentLink($this->container);
        $link->setUrl($this->userInput);

        return $link;
    }

    /**
     * Check file extension
     *
     * @access protected
     * @param  string $extension
     * @return boolean
     */
    protected function isValidExtension($extension)
    {
        $extension = strtolower($extension);

        foreach ($this->extensions as $ext) {
            if ($extension === $ext) {
                return false;
            }
        }

        return true;
    }
}
