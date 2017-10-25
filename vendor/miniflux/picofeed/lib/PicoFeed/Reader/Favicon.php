<?php

namespace PicoFeed\Reader;

use DOMXPath;
use PicoFeed\Base;
use PicoFeed\Client\Client;
use PicoFeed\Client\ClientException;
use PicoFeed\Client\Url;
use PicoFeed\Logging\Logger;
use PicoFeed\Parser\XmlParser;

/**
 * Favicon class.
 *
 * https://en.wikipedia.org/wiki/Favicon
 *
 * @author  Frederic Guillot
 */
class Favicon extends Base
{
    /**
     * Valid types for favicon (supported by browsers).
     *
     * @var array
     */
    private $types = array(
        'image/png',
        'image/gif',
        'image/x-icon',
        'image/jpeg',
        'image/jpg',
        'image/svg+xml',
    );

    /**
     * Icon binary content.
     *
     * @var string
     */
    private $content = '';

    /**
     * Icon content type.
     *
     * @var string
     */
    private $content_type = '';

    /**
     * Get the icon file content (available only after the download).
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Get the icon file type (available only after the download).
     *
     * @return string
     */
    public function getType()
    {
        foreach ($this->types as $type) {
            if (strpos($this->content_type, $type) === 0) {
                return $type;
            }
        }

        return 'image/x-icon';
    }

    /**
     * Get data URI (http://en.wikipedia.org/wiki/Data_URI_scheme).
     *
     * @return string
     */
    public function getDataUri()
    {
        if (empty($this->content)) {
            return '';
        }

        return sprintf(
            'data:%s;base64,%s',
            $this->getType(),
            base64_encode($this->content)
        );
    }

    /**
     * Download and check if a resource exists.
     *
     * @param string $url URL
     * @return \PicoFeed\Client\Client Client instance
     */
    public function download($url)
    {
        $client = Client::getInstance();
        $client->setConfig($this->config);

        Logger::setMessage(get_called_class().' Download => '.$url);

        try {
            $client->execute($url);
        } catch (ClientException $e) {
            Logger::setMessage(get_called_class().' Download Failed => '.$e->getMessage());
        }

        return $client;
    }

    /**
     * Check if a remote file exists.
     *
     * @param string $url URL
     * @return bool
     */
    public function exists($url)
    {
        return $this->download($url)->getContent() !== '';
    }

    /**
     * Get the icon link for a website.
     *
     * @param string $website_link URL
     * @param string $favicon_link optional URL
     * @return string
     */
    public function find($website_link, $favicon_link = '')
    {
        $website = new Url($website_link);

        if ($favicon_link !== '') {
            $icons = array($favicon_link);
        } else {
            $icons = $this->extract($this->download($website->getBaseUrl('/'))->getContent());
            $icons[] = $website->getBaseUrl('/favicon.ico');
        }

        foreach ($icons as $icon_link) {
            $icon_link = Url::resolve($icon_link, $website);
            $resource = $this->download($icon_link);
            $this->content = $resource->getContent();
            $this->content_type = $resource->getContentType();

            if ($this->content !== '') {
                return $icon_link;
            } elseif ($favicon_link !== '') {
                return $this->find($website_link);
            }
        }

        return '';
    }

    /**
     * Extract the icon links from the HTML.
     *
     * @param string $html HTML
     * @return array
     */
    public function extract($html)
    {
        $icons = array();

        if (empty($html)) {
            return $icons;
        }

        $dom = XmlParser::getHtmlDocument($html);

        $xpath = new DOMXpath($dom);
        $elements = $xpath->query('//link[@rel="icon" or @rel="shortcut icon" or @rel="Shortcut Icon" or @rel="icon shortcut"]');

        for ($i = 0; $i < $elements->length; ++$i) {
            $icons[] = $elements->item($i)->getAttribute('href');
        }

        return $icons;
    }
}
