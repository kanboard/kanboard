<?php

namespace PicoFeed\Syndication;

use DateTime;
use DOMDocument;
use DOMElement;

/**
 * Class AtomHelper
 *
 * @package PicoFeed\Syndication
 * @author  Frederic Guillot
 */
class AtomHelper
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
     * @return AtomHelper
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
     * @return AtomHelper
     */
    public function buildTitle(DOMElement $element, $title)
    {
        return $this->buildNode($element, 'title', $title);
    }

    /**
     * Build id
     *
     * @access public
     * @param  DOMElement $element
     * @param  string     $id
     * @return AtomHelper
     */
    public function buildId(DOMElement $element, $id)
    {
        return $this->buildNode($element, 'id', $id);
    }

    /**
     * Build date element
     *
     * @access public
     * @param  DOMElement $element
     * @param  DateTime   $date
     * @param  string     $type
     * @return AtomHelper
     */
    public function buildDate(DOMElement $element, DateTime $date, $type = 'updated')
    {
        return $this->buildNode($element, $type, $date->format(DateTime::ATOM));
    }

    /**
     * Build link element
     *
     * @access public
     * @param  DOMElement $element
     * @param  string     $url
     * @param  string     $rel
     * @param  string     $type
     * @return AtomHelper
     */
    public function buildLink(DOMElement $element, $url, $rel = 'alternate', $type = 'text/html')
    {
        $node = $this->document->createElement('link');
        $node->setAttribute('rel', $rel);
        $node->setAttribute('type', $type);
        $node->setAttribute('href', $url);
        $element->appendChild($node);

        return $this;
    }

    /**
     * Build author element
     *
     * @access public
     * @param  DOMElement $element
     * @param  string     $authorName
     * @param  string     $authorEmail
     * @param  string     $authorUrl
     * @return AtomHelper
     */
    public function buildAuthor(DOMElement $element, $authorName, $authorEmail, $authorUrl)
    {
        if (!empty($authorName)) {
            $author = $this->document->createElement('author');
            $this->buildNode($author, 'name', $authorName);

            if (!empty($authorEmail)) {
                $this->buildNode($author, 'email', $authorEmail);
            }

            if (!empty($authorUrl)) {
                $this->buildNode($author, 'uri', $authorUrl);
            }

            $element->appendChild($author);
        }

        return $this;
    }
}
