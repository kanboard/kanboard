<?php

namespace PicoFeed\Syndication;

use DateTime;
use DOMDocument;
use DOMElement;

/**
 * Class Rss20Helper
 *
 * @package PicoFeed\Syndication
 * @author  Frederic Guillot
 */
class Rss20Helper
{
    /**
     * @var DOMDocument
     */
    protected $document;

    /**
     * Constructor
     *
     * @param DOMDocument $document
     */
    public function __construct(DOMDocument $document)
    {
        $this->document = $document;
    }

    /**
     * Build node
     *
     * @access public
     * @param  DOMElement $element
     * @param  string     $tag
     * @param  string     $value
     * @return $this
     */
    public function buildNode(DOMElement $element, $tag, $value)
    {
        $node = $this->document->createElement($tag);
        $node->appendChild($this->document->createTextNode($value));
        $element->appendChild($node);
        return $this;
    }

    /**
     * Build title
     *
     * @access public
     * @param  DOMElement $element
     * @param  string     $title
     * @return $this
     */
    public function buildTitle(DOMElement $element, $title)
    {
        return $this->buildNode($element, 'title', $title);
    }

    /**
     * Build date element
     *
     * @access public
     * @param  DOMElement $element
     * @param  DateTime   $date
     * @param  string     $type
     * @return $this
     */
    public function buildDate(DOMElement $element, DateTime $date, $type = 'pubDate')
    {
        return $this->buildNode($element, $type, $date->format(DateTime::RSS));
    }

    /**
     * Build link element
     *
     * @access public
     * @param  DOMElement $element
     * @param  string     $url
     * @return $this
     */
    public function buildLink(DOMElement $element, $url)
    {
        return $this->buildNode($element, 'link', $url);
    }

    /**
     * Build author element
     *
     * @access public
     * @param  DOMElement $element
     * @param  string     $tag
     * @param  string     $authorName
     * @param  string     $authorEmail
     * @return $this
     */
    public function buildAuthor(DOMElement $element, $tag, $authorName, $authorEmail)
    {
        if (!empty($authorName)) {
            $value = '';

            if (!empty($authorEmail)) {
                $value .= $authorEmail.' ('.$authorName.')';
            } else {
                $value = $authorName;
            }

            $this->buildNode($element, $tag, $value);
        }

        return $this;
    }
}
