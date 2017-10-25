<?php

namespace PicoFeed\Scraper;

use DomDocument;
use DOMXPath;
use PicoFeed\Logging\Logger;
use PicoFeed\Parser\XmlParser;

/**
 * Candidate Parser.
 *
 * @author  Frederic Guillot
 */
class CandidateParser implements ParserInterface
{
    private $dom;
    private $xpath;

    /**
     * List of attributes to try to get the content, order is important, generic terms at the end.
     *
     * @var array
     */
    private $candidatesAttributes = array(
        'articleBody',
        'articlebody',
        'article-body',
        'articleContent',
        'articlecontent',
        'article-content',
        'articlePage',
        'post-content',
        'post_content',
        'entry-content',
        'entry-body',
        'main-content',
        'story_content',
        'storycontent',
        'entryBox',
        'entrytext',
        'comic',
        'post',
        'article',
        'content',
        'main',
    );

    /**
     * List of attributes to strip.
     *
     * @var array
     */
    private $stripAttributes = array(
        'comment',
        'share',
        'links',
        'toolbar',
        'fb',
        'footer',
        'credit',
        'bottom',
        'nav',
        'header',
        'social',
        'tag',
        'metadata',
        'entry-utility',
        'related-posts',
        'tweet',
        'categories',
        'post_title',
        'by_line',
        'byline',
        'sponsors',
    );

    /**
     * Tags to remove.
     *
     * @var array
     */
    private $stripTags = array(
        'nav',
        'header',
        'footer',
        'aside',
        'form',
    );

    /**
     * Constructor.
     *
     * @param string $html
     */
    public function __construct($html)
    {
        $this->dom = XmlParser::getHtmlDocument('<?xml version="1.0" encoding="UTF-8">'.$html);
        $this->xpath = new DOMXPath($this->dom);
    }

    /**
     * Get the relevant content with the list of potential attributes.
     *
     * @return string
     */
    public function execute()
    {
        $content = $this->findContentWithCandidates();

        if (strlen($content) < 200) {
            $content = $this->findContentWithArticle();
        }

        if (strlen($content) < 50) {
            $content = $this->findContentWithBody();
        }

        return $this->stripGarbage($content);
    }

    /**
     * Find content based on the list of tag candidates.
     *
     * @return string
     */
    public function findContentWithCandidates()
    {
        foreach ($this->candidatesAttributes as $candidate) {
            Logger::setMessage(get_called_class().': Try this candidate: "'.$candidate.'"');

            $nodes = $this->xpath->query('//*[(contains(@class, "'.$candidate.'") or @id="'.$candidate.'") and not (contains(@class, "nav") or contains(@class, "page"))]');

            if ($nodes !== false && $nodes->length > 0) {
                Logger::setMessage(get_called_class().': Find candidate "'.$candidate.'"');

                return $this->dom->saveXML($nodes->item(0));
            }
        }

        return '';
    }

    /**
     * Find <article/> tag.
     *
     * @return string
     */
    public function findContentWithArticle()
    {
        $nodes = $this->xpath->query('//article');

        if ($nodes !== false && $nodes->length > 0) {
            Logger::setMessage(get_called_class().': Find <article/> tag');

            return $this->dom->saveXML($nodes->item(0));
        }

        return '';
    }

    /**
     * Find <body/> tag.
     *
     * @return string
     */
    public function findContentWithBody()
    {
        $nodes = $this->xpath->query('//body');

        if ($nodes !== false && $nodes->length > 0) {
            Logger::setMessage(get_called_class().' Find <body/>');

            return $this->dom->saveXML($nodes->item(0));
        }

        return '';
    }

    /**
     * Strip useless tags.
     *
     * @param string $content
     *
     * @return string
     */
    public function stripGarbage($content)
    {
        $dom = XmlParser::getDomDocument($content);

        if ($dom !== false) {
            $xpath = new DOMXPath($dom);

            $this->stripTags($xpath);
            $this->stripAttributes($dom, $xpath);

            $content = $dom->saveXML($dom->documentElement);
        }

        return $content;
    }

    /**
     * Remove blacklisted tags.
     *
     * @param DOMXPath $xpath
     */
    public function stripTags(DOMXPath $xpath)
    {
        foreach ($this->stripTags as $tag) {
            $nodes = $xpath->query('//'.$tag);

            if ($nodes !== false && $nodes->length > 0) {
                Logger::setMessage(get_called_class().': Strip tag: "'.$tag.'"');

                foreach ($nodes as $node) {
                    $node->parentNode->removeChild($node);
                }
            }
        }
    }

    /**
     * Remove blacklisted attributes.
     *
     * @param DomDocument $dom
     * @param DOMXPath    $xpath
     */
    public function stripAttributes(DomDocument $dom, DOMXPath $xpath)
    {
        foreach ($this->stripAttributes as $attribute) {
            $nodes = $xpath->query('//*[contains(@class, "'.$attribute.'") or contains(@id, "'.$attribute.'")]');

            if ($nodes !== false && $nodes->length > 0) {
                Logger::setMessage(get_called_class().': Strip attribute: "'.$attribute.'"');

                foreach ($nodes as $node) {
                    if ($this->shouldRemove($dom, $node)) {
                        $node->parentNode->removeChild($node);
                    }
                }
            }
        }
    }

    /**
     * Find link for next page of the article.
     *
     * @return string
     */
    public function findNextLink()
    {
        return null;
    }

    /**
     * Return false if the node should not be removed.
     *
     * @param DomDocument $dom
     * @param DomNode     $node
     *
     * @return bool
     */
    public function shouldRemove(DomDocument $dom, $node)
    {
        $document_length = strlen($dom->textContent);
        $node_length = strlen($node->textContent);

        if ($document_length === 0) {
            return true;
        }

        $ratio = $node_length * 100 / $document_length;

        if ($ratio >= 90) {
            Logger::setMessage(get_called_class().': Should not remove this node ('.$node->nodeName.') ratio: '.$ratio.'%');

            return false;
        }

        return true;
    }
}
