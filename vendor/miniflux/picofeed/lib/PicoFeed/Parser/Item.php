<?php

namespace PicoFeed\Parser;

/**
 * Feed Item.
 *
 * @package PicoFeed\Parser
 * @author  Frederic Guillot
 */
class Item
{
    /**
     * List of known RTL languages.
     *
     * @var string[]
     */
    public $rtl = array(
        'ar',  // Arabic (ar-**)
        'fa',  // Farsi (fa-**)
        'ur',  // Urdu (ur-**)
        'ps',  // Pashtu (ps-**)
        'syr', // Syriac (syr-**)
        'dv',  // Divehi (dv-**)
        'he',  // Hebrew (he-**)
        'yi',  // Yiddish (yi-**)
    );

    /**
     * Item id.
     *
     * @var string
     */
    public $id = '';

    /**
     * Item title.
     *
     * @var string
     */
    public $title = '';

    /**
     * Item url.
     *
     * @var string
     */
    public $url = '';

    /**
     * Item author.
     *
     * @var string
     */
    public $author = '';

    /**
     * Item date.
     *
     * @var \DateTime
     */
    public $date = null;

    /**
     * Item published date.
     *
     * @var \DateTime
     */
    public $publishedDate = null;

    /**
     * Item updated date.
     *
     * @var \DateTime
     */
    public $updatedDate = null;

    /**
     * Item content.
     *
     * @var string
     */
    public $content = '';

    /**
     * Item enclosure url.
     *
     * @var string
     */
    public $enclosureUrl = '';

    /**
     * Item enclusure type.
     *
     * @var string
     */
    public $enclosureType = '';

    /**
     * Item language.
     *
     * @var string
     */
    public $language = '';

    /**
     * Item categories.
     *
     * @var array
     */
    public $categories = array();

    /**
     * Raw XML.
     *
     * @var \SimpleXMLElement
     */
    public $xml;

    /**
     * List of namespaces.
     *
     * @var array
     */
    public $namespaces = array();

    /**
     * Check if a XML namespace exists
     *
     * @access public
     * @param  string $namespace
     * @return bool
     */
    public function hasNamespace($namespace)
    {
        return array_key_exists($namespace, $this->namespaces);
    }

    /**
     * Get specific XML tag or attribute value.
     *
     * @param string $tag       Tag name (examples: guid, media:content)
     * @param string $attribute Tag attribute
     *
     * @return array|false Tag values or error
     */
    public function getTag($tag, $attribute = '')
    {
        if ($attribute !== '') {
            $attribute = '/@'.$attribute;
        }

        $query = './/'.$tag.$attribute;
        $elements = XmlParser::getXPathResult($this->xml, $query, $this->namespaces);

        if ($elements === false) { // xPath error
            return false;
        }

        return array_map(function ($element) { return (string) $element;}, $elements);
    }

    /**
     * Return item information.
     *
     * @return string
     */
    public function __toString()
    {
        $output = '';

        foreach (array('id', 'title', 'url', 'language', 'author', 'enclosureUrl', 'enclosureType') as $property) {
            $output .= 'Item::'.$property.' = '.$this->$property.PHP_EOL;
        }

        $publishedDate = $this->publishedDate != null ? $this->publishedDate->format(DATE_RFC822) : null;
        $updatedDate = $this->updatedDate != null ? $this->updatedDate->format(DATE_RFC822) : null;

        $categoryString = $this->categories != null ? implode(',', $this->categories) : null;

        $output .= 'Item::date = '.$this->date->format(DATE_RFC822).PHP_EOL;
        $output .= 'Item::publishedDate = '.$publishedDate.PHP_EOL;
        $output .= 'Item::updatedDate = '.$updatedDate.PHP_EOL;
        $output .= 'Item::isRTL() = '.($this->isRTL() ? 'true' : 'false').PHP_EOL;
        $output .= 'Item::categories = ['.$categoryString.']'.PHP_EOL;
        $output .= 'Item::content = '.strlen($this->content).' bytes'.PHP_EOL;

        return $output;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get URL
     *
     * @access public
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set URL
     *
     * @access public
     * @param  string $url
     * @return Item
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Get id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get date.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Get published date.
     *
     * @return \DateTime
     */
    public function getPublishedDate()
    {
        return $this->publishedDate;
    }

    /**
     * Get updated date.
     *
     * @return \DateTime
     */
    public function getUpdatedDate()
    {
        return $this->updatedDate;
    }

    /**
     * Get content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set content
     *
     * @access public
     * @param  string $value
     * @return Item
     */
    public function setContent($value)
    {
        $this->content = $value;
        return $this;
    }

    /**
     * Get enclosure url.
     *
     * @return string
     */
    public function getEnclosureUrl()
    {
        return $this->enclosureUrl;
    }

    /**
     * Get enclosure type.
     *
     * @return string
     */
    public function getEnclosureType()
    {
        return $this->enclosureType;
    }

    /**
     * Get language.
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Get categories.
     *
     * @return string
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Get author.
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Return true if the item is "Right to Left".
     *
     * @return bool
     */
    public function isRTL()
    {
        return Parser::isLanguageRTL($this->language);
    }

    /**
     * Set item id.
     *
     * @param string $id
     * @return Item
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set item title.
     *
     * @param string $title
     * @return Item
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Set author.
     *
     * @param string $author
     * @return Item
     */
    public function setAuthor($author)
    {
        $this->author = $author;
        return $this;
    }

    /**
     * Set item date.
     *
     * @param \DateTime $date
     * @return Item
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Set item published date.
     *
     * @param \DateTime $publishedDate
     * @return Item
     */
    public function setPublishedDate($publishedDate)
    {
        $this->publishedDate = $publishedDate;
        return $this;
    }

    /**
     * Set item updated date.
     *
     * @param \DateTime $updatedDate
     * @return Item
     */
    public function setUpdatedDate($updatedDate)
    {
        $this->updatedDate = $updatedDate;
        return $this;
    }

    /**
     * Set enclosure url.
     *
     * @param string $enclosureUrl
     * @return Item
     */
    public function setEnclosureUrl($enclosureUrl)
    {
        $this->enclosureUrl = $enclosureUrl;
        return $this;
    }

    /**
     * Set enclosure type.
     *
     * @param string $enclosureType
     * @return Item
     */
    public function setEnclosureType($enclosureType)
    {
        $this->enclosureType = $enclosureType;
        return $this;
    }

    /**
     * Set item language.
     *
     * @param string $language
     * @return Item
     */
    public function setLanguage($language)
    {
        $this->language = $language;
        return $this;
    }

    /**
     * Set item categories.
     *
     * @param array $categories
     * @return Item
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
        return $this;
    }

    /**
     * Set item categories from xml.
     *
     * @param SimpleXMLElement[] $categories
     * @return Item
     */
    public function setCategoriesFromXml($categories)
    {
        if ($categories !== false) {
            $this->setCategories(
                array_map(
                    function ($element) {
                        return trim((string) $element);
                    },
                    $categories
                )
            );
        } else {
            $categories = array();
        }
        return $this;
    }

    /**
     * Set raw XML.
     *
     * @param \SimpleXMLElement $xml
     * @return Item
     */
    public function setXml($xml)
    {
        $this->xml = $xml;
        return $this;
    }

    /**
     * Get raw XML.
     *
     * @return \SimpleXMLElement
     */
    public function getXml()
    {
        return $this->xml;
    }

    /**
     * Set XML namespaces.
     *
     * @param array $namespaces
     * @return Item
     */
    public function setNamespaces($namespaces)
    {
        $this->namespaces = $namespaces;
        return $this;
    }

    /**
     * Get XML namespaces.
     *
     * @return array
     */
    public function getNamespaces()
    {
        return $this->namespaces;
    }
}
