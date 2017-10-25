<?php

namespace PicoFeed\Serialization;

use PicoFeed\Parser\MalformedXmlException;
use PicoFeed\Parser\XmlParser;
use SimpleXMLElement;

/**
 * Class SubscriptionListParser
 *
 * @package PicoFeed\Serialization
 * @author  Frederic Guillot
 */
class SubscriptionListParser
{
    /**
     * @var SubscriptionList
     */
    protected $subscriptionList;

    /**
     * @var string
     */
    protected $data;

    /**
     * Constructor
     *
     * @access public
     * @param  string $data
     */
    public function __construct($data)
    {
        $this->subscriptionList = new SubscriptionList();
        $this->data = trim($data);
    }

    /**
     * Get object instance
     *
     * @static
     * @access public
     * @param  string $data
     * @return SubscriptionListParser
     */
    public static function create($data)
    {
        return new static($data);
    }

    /**
     * Parse a subscription list entry
     *
     * @access public
     * @throws MalformedXmlException
     * @return SubscriptionList
     */
    public function parse()
    {
        $xml = XmlParser::getSimpleXml($this->data);

        if (! $xml || !isset($xml->head) || !isset($xml->body)) {
            throw new MalformedXmlException('Unable to parse OPML file: invalid XML');
        }

        $this->parseTitle($xml->head);
        $this->parseEntries($xml->body);

        return $this->subscriptionList;
    }

    /**
     * Parse title
     *
     * @access protected
     * @param  SimpleXMLElement $xml
     */
    protected function parseTitle(SimpleXMLElement $xml)
    {
        $this->subscriptionList->setTitle((string) $xml->title);
    }

    /**
     * Parse entries
     *
     * @access protected
     * @param  SimpleXMLElement $body
     */
    private function parseEntries(SimpleXMLElement $body)
    {
        foreach ($body->outline as $outlineElement) {
            if (isset($outlineElement->outline)) {
                $this->parseEntries($outlineElement);
            } else {
                $this->subscriptionList->subscriptions[] = SubscriptionParser::create($body, $outlineElement)->parse();
            }
        }
    }
}
