<?php

namespace PicoFeed\Parser;

use PicoFeed\Processor\ContentFilterProcessor;
use PicoFeed\Processor\ContentGeneratorProcessor;
use PicoFeed\Processor\ItemPostProcessor;
use PicoFeed\Processor\ScraperProcessor;
use SimpleXMLElement;
use PicoFeed\Client\Url;
use PicoFeed\Encoding\Encoding;
use PicoFeed\Filter\Filter;
use PicoFeed\Logging\Logger;

/**
 * Base parser class.
 *
 * @package PicoFeed\Parser
 * @author  Frederic Guillot
 */
abstract class Parser implements ParserInterface
{
    /**
     * Config object.
     *
     * @var \PicoFeed\Config\Config
     */
    private $config;

    /**
     * DateParser object.
     *
     * @var \PicoFeed\Parser\DateParser
     */
    private $dateParser;

    /**
     * Hash algorithm used to generate item id, any value supported by PHP, see hash_algos().
     *
     * @var string
     */
    private $hash_algo = 'sha256';

    /**
     * Feed content (XML data).
     *
     * @var string
     */
    protected $content = '';

    /**
     * Fallback url.
     *
     * @var string
     */
    protected $fallback_url = '';

    /**
     * XML namespaces supported by parser.
     *
     * @var array
     */
    protected $namespaces = array();

    /**
     * XML namespaces used in document.
     *
     * @var array
     */
    protected $used_namespaces = array();

    /**
     * Item Post Processor instance
     *
     * @access private
     * @var ItemPostProcessor
     */
    private $itemPostProcessor;

    /**
     * Constructor.
     *
     * @param string $content       Feed content
     * @param string $http_encoding HTTP encoding (headers)
     * @param string $fallback_url  Fallback url when the feed provide relative or broken url
     */
    public function __construct($content, $http_encoding = '', $fallback_url = '')
    {
        $this->fallback_url = $fallback_url;
        $xml_encoding = XmlParser::getEncodingFromXmlTag($content);

        // Strip XML tag to avoid multiple encoding/decoding in the next XML processing
        $this->content = Filter::stripXmlTag($content);

        // Encode everything in UTF-8
        Logger::setMessage(get_called_class().': HTTP Encoding "'.$http_encoding.'" ; XML Encoding "'.$xml_encoding.'"');
        $this->content = Encoding::convert($this->content, $xml_encoding ?: $http_encoding);

        $this->itemPostProcessor = new ItemPostProcessor($this->config);
        $this->itemPostProcessor->register(new ContentGeneratorProcessor($this->config));
        $this->itemPostProcessor->register(new ContentFilterProcessor($this->config));
    }

    /**
     * Parse the document.
     *
     * @return \PicoFeed\Parser\Feed
     */
    public function execute()
    {
        Logger::setMessage(get_called_class().': begin parsing');

        $xml = XmlParser::getSimpleXml($this->content);

        if ($xml === false) {
            Logger::setMessage(get_called_class().': Applying XML workarounds');
            $this->content = Filter::normalizeData($this->content);
            $xml = XmlParser::getSimpleXml($this->content);

            if ($xml === false) {
                Logger::setMessage(get_called_class().': XML parsing error');
                Logger::setMessage(XmlParser::getErrors());
                throw new MalformedXmlException('XML parsing error');
            }
        }

        $this->used_namespaces = $xml->getNamespaces(true);
        $xml = $this->registerSupportedNamespaces($xml);

        $feed = new Feed();

        $this->findFeedUrl($xml, $feed);
        $this->checkFeedUrl($feed);

        $this->findSiteUrl($xml, $feed);
        $this->checkSiteUrl($feed);

        $this->findFeedTitle($xml, $feed);
        $this->findFeedDescription($xml, $feed);
        $this->findFeedLanguage($xml, $feed);
        $this->findFeedId($xml, $feed);
        $this->findFeedDate($xml, $feed);
        $this->findFeedLogo($xml, $feed);
        $this->findFeedIcon($xml, $feed);

        foreach ($this->getItemsTree($xml) as $entry) {
            $entry = $this->registerSupportedNamespaces($entry);

            $item = new Item();
            $item->xml = $entry;
            $item->namespaces = $this->used_namespaces;

            $this->findItemAuthor($xml, $entry, $item);

            $this->findItemUrl($entry, $item);
            $this->checkItemUrl($feed, $item);

            $this->findItemTitle($entry, $item);
            $this->findItemContent($entry, $item);

            // Id generation can use the item url/title/content (order is important)
            $this->findItemId($entry, $item, $feed);
            $this->findItemDate($entry, $item, $feed);
            $this->findItemEnclosure($entry, $item, $feed);
            $this->findItemLanguage($entry, $item, $feed);
            $this->findItemCategories($entry, $item, $feed);

            $this->itemPostProcessor->execute($feed, $item);
            $feed->items[] = $item;
        }

        Logger::setMessage(get_called_class().PHP_EOL.$feed);

        return $feed;
    }

    /**
     * Check if the feed url is correct.
     *
     * @param Feed $feed Feed object
     */
    public function checkFeedUrl(Feed $feed)
    {
        if ($feed->getFeedUrl() === '') {
            $feed->feedUrl = $this->fallback_url;
        } else {
            $feed->feedUrl = Url::resolve($feed->getFeedUrl(), $this->fallback_url);
        }
    }

    /**
     * Check if the site url is correct.
     *
     * @param Feed $feed Feed object
     */
    public function checkSiteUrl(Feed $feed)
    {
        if ($feed->getSiteUrl() === '') {
            $feed->siteUrl = Url::base($feed->getFeedUrl());
        } else {
            $feed->siteUrl = Url::resolve($feed->getSiteUrl(), $this->fallback_url);
        }
    }

    /**
     * Check if the item url is correct.
     *
     * @param Feed $feed Feed object
     * @param Item $item Item object
     */
    public function checkItemUrl(Feed $feed, Item $item)
    {
        $item->url = Url::resolve($item->getUrl(), $feed->getSiteUrl());
    }

    /**
     * Find the item date.
     *
     * @param SimpleXMLElement      $entry Feed item
     * @param Item                  $item  Item object
     * @param \PicoFeed\Parser\Feed $feed  Feed object
     */
    public function findItemDate(SimpleXMLElement $entry, Item $item, Feed $feed)
    {
        $this->findItemPublishedDate($entry, $item, $feed);
        $this->findItemUpdatedDate($entry, $item, $feed);

        if ($item->getPublishedDate() === null) {
            // Use the updated date if available, otherwise use the feed date
            $item->setPublishedDate($item->getUpdatedDate() ?: $feed->getDate());
        }

        if ($item->getUpdatedDate() === null) {
            // Use the published date as fallback
            $item->setUpdatedDate($item->getPublishedDate());
        }

        // Use the most recent of published and updated dates
        $item->setDate(max($item->getPublishedDate(), $item->getUpdatedDate()));
    }

    /**
     * Get Item Post Processor instance
     *
     * @access public
     * @return ItemPostProcessor
     */
    public function getItemPostProcessor()
    {
        return $this->itemPostProcessor;
    }

    /**
     * Get DateParser instance
     *
     * @access public
     * @return DateParser
     */
    public function getDateParser()
    {
        if ($this->dateParser === null) {
            $this->dateParser = new DateParser($this->config);
        }

        return $this->dateParser;
    }

    /**
     * Generate a unique id for an entry (hash all arguments).
     *
     * @return string
     */
    public function generateId()
    {
        return hash($this->hash_algo, implode(func_get_args()));
    }

    /**
     * Return true if the given language is "Right to Left".
     *
     * @static
     *
     * @param string $language Language: fr-FR, en-US
     *
     * @return bool
     */
    public static function isLanguageRTL($language)
    {
        $language = strtolower($language);

        $rtl_languages = array(
            'ar', // Arabic (ar-**)
            'fa', // Farsi (fa-**)
            'ur', // Urdu (ur-**)
            'ps', // Pashtu (ps-**)
            'syr', // Syriac (syr-**)
            'dv', // Divehi (dv-**)
            'he', // Hebrew (he-**)
            'yi', // Yiddish (yi-**)
        );

        foreach ($rtl_languages as $prefix) {
            if (strpos($language, $prefix) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set Hash algorithm used for id generation.
     *
     * @param string $algo Algorithm name
     * @return \PicoFeed\Parser\Parser
     */
    public function setHashAlgo($algo)
    {
        $this->hash_algo = $algo ?: $this->hash_algo;
        return $this;
    }

    /**
     * Set config object.
     *
     * @param \PicoFeed\Config\Config $config Config instance
     *
     * @return \PicoFeed\Parser\Parser
     */
    public function setConfig($config)
    {
        $this->config = $config;
        $this->itemPostProcessor->setConfig($config);
        return $this;
    }

    /**
     * Enable the content grabber.
     *
     * @return \PicoFeed\Parser\Parser
     */
    public function disableContentFiltering()
    {
        $this->itemPostProcessor->unregister('PicoFeed\Processor\ContentFilterProcessor');
        return $this;
    }

    /**
     * Enable the content grabber.
     *
     * @param bool          $needsRuleFile   true if only pages with rule files should be
     *                                       scraped
     * @param null|\Closure $scraperCallback Callback function that gets called for each
     *                                       scraper execution
     *
     * @return \PicoFeed\Parser\Parser
     */
    public function enableContentGrabber($needsRuleFile = false, $scraperCallback = null)
    {
        $processor = new ScraperProcessor($this->config);

        if ($needsRuleFile) {
            $processor->getScraper()->disableCandidateParser();
        }

        if ($scraperCallback !== null) {
            $processor->setExecutionCallback($scraperCallback);
        }

        $this->itemPostProcessor->register($processor);
        return $this;
    }

    /**
     * Set ignored URLs for the content grabber.
     *
     * @param array $urls URLs
     *
     * @return \PicoFeed\Parser\Parser
     */
    public function setGrabberIgnoreUrls(array $urls)
    {
        $this->itemPostProcessor->getProcessor('PicoFeed\Processor\ScraperProcessor')->ignoreUrls($urls);
        return $this;
    }

    /**
     * Register all supported namespaces to be used within an xpath query.
     *
     * @param SimpleXMLElement $xml Feed xml
     *
     * @return SimpleXMLElement
     */
    public function registerSupportedNamespaces(SimpleXMLElement $xml)
    {
        foreach ($this->namespaces as $prefix => $ns) {
            $xml->registerXPathNamespace($prefix, $ns);
        }

        return $xml;
    }


}
