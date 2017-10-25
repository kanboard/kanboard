<?php

namespace PicoFeed\Scraper;

use PicoFeed\Base;
use PicoFeed\Client\Client;
use PicoFeed\Client\ClientException;
use PicoFeed\Client\Url;
use PicoFeed\Encoding\Encoding;
use PicoFeed\Filter\Filter;
use PicoFeed\Logging\Logger;
use PicoFeed\Parser\XmlParser;

/**
 * Scraper class.
 *
 * @author  Frederic Guillot
 */
class Scraper extends Base
{
    /**
     * URL.
     *
     * @var string
     */
    private $url = '';

    /**
     * Relevant content.
     *
     * @var string
     */
    private $content = '';

    /**
     * HTML content.
     *
     * @var string
     */
    private $html = '';

    /**
     * HTML content encoding.
     *
     * @var string
     */
    private $encoding = '';

    /**
     * Flag to enable candidates parsing.
     *
     * @var bool
     */
    private $enableCandidateParser = true;

    /**
     * Disable candidates parsing.
     *
     * @return Scraper
     */
    public function disableCandidateParser()
    {
        $this->enableCandidateParser = false;
        return $this;
    }

    /**
     * Get encoding.
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Set encoding.
     *
     * @param string $encoding
     *
     * @return Scraper
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;

        return $this;
    }

    /**
     * Get URL to download.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set URL to download.
     *
     * @param string $url URL
     *
     * @return Scraper
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Return true if the scraper found relevant content.
     *
     * @return bool
     */
    public function hasRelevantContent()
    {
        return !empty($this->content);
    }

    /**
     * Get relevant content.
     *
     * @return string
     */
    public function getRelevantContent()
    {
        return $this->content;
    }

    /**
     * Get raw content (unfiltered).
     *
     * @return string
     */
    public function getRawContent()
    {
        return $this->html;
    }

    /**
     * Set raw content (unfiltered).
     *
     * @param string $html
     *
     * @return Scraper
     */
    public function setRawContent($html)
    {
        $this->html = $html;

        return $this;
    }

    /**
     * Get filtered relevant content.
     *
     * @return string
     */
    public function getFilteredContent()
    {
        $filter = Filter::html($this->content, $this->url);
        $filter->setConfig($this->config);

        return $filter->execute();
    }

    /**
     * Download the HTML content.
     *
     * @return bool
     */
    public function download()
    {
        if (!empty($this->url)) {

            // Clear everything
            $this->html = '';
            $this->content = '';
            $this->encoding = '';

            try {
                $client = Client::getInstance();
                $client->setConfig($this->config);
                $client->setTimeout($this->config->getGrabberTimeout());
                $client->setUserAgent($this->config->getGrabberUserAgent());
                $client->execute($this->url);

                $this->url = $client->getUrl();
                $this->html = $client->getContent();
                $this->encoding = $client->getEncoding();

                return true;
            } catch (ClientException $e) {
                Logger::setMessage(get_called_class().': '.$e->getMessage());
            }
        }

        return false;
    }

    /**
     * Execute the scraper.
     */
    public function execute($pageContent = '', $recursionDepth = 0)
    {
        $this->html = '';
        $this->encoding = '';
        $this->content = '';
        $this->download();
        $this->prepareHtml();

        $parser = $this->getParser();

        if ($parser !== null) {
            $maxRecursions = $this->config->getMaxRecursions();
            if(!isset($maxRecursions)){
                $maxRecursions = 25;
            }
            $pageContent .= $parser->execute();
            // check if there is a link to next page and recursively get content (max 25 pages)
            if((($nextLink = $parser->findNextLink()) !== null) && $recursionDepth < $maxRecursions){
                $nextLink = Url::resolve($nextLink,$this->url);
                $this->setUrl($nextLink);
                $this->execute($pageContent,$recursionDepth+1);
            }
            else{
                $this->content = $pageContent;
            }
            Logger::setMessage(get_called_class().': Content length: '.strlen($this->content).' bytes');
        }
    }

    /**
     * Get the parser.
     *
     * @return ParserInterface
     */
    public function getParser()
    {
        $ruleLoader = new RuleLoader($this->config);
        $rules = $ruleLoader->getRules($this->url);

        if (!empty($rules['grabber'])) {
            Logger::setMessage(get_called_class().': Parse content with rules');

            foreach ($rules['grabber'] as $pattern => $rule) {
                $url = new Url($this->url);
                $sub_url = $url->getFullPath();

                if (preg_match($pattern, $sub_url)) {
                    Logger::setMessage(get_called_class().': Matched url '.$sub_url);
                    return new RuleParser($this->html, $rule);
                }
            }
        } elseif ($this->enableCandidateParser) {
            Logger::setMessage(get_called_class().': Parse content with candidates');
        }

        return new CandidateParser($this->html);
    }

    /**
     * Normalize encoding and strip head tag.
     */
    public function prepareHtml()
    {
        $html_encoding = XmlParser::getEncodingFromMetaTag($this->html);

        $this->html = Encoding::convert($this->html, $html_encoding ?: $this->encoding);
        $this->html = Filter::stripHeadTags($this->html);

        Logger::setMessage(get_called_class().': HTTP Encoding "'.$this->encoding.'" ; HTML Encoding "'.$html_encoding.'"');
    }
}
