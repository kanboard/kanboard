<?php

namespace PicoFeed\Filter;

use PicoFeed\Client\Url;

/**
 * Attribute Filter class.
 *
 * @author  Frederic Guillot
 */
class Attribute
{
    /**
     * Image proxy url.
     *
     * @var string
     */
    private $image_proxy_url = '';

    /**
     * Image proxy callback.
     *
     * @var \Closure|null
     */
    private $image_proxy_callback = null;

    /**
     * limits the image proxy usage to this protocol.
     *
     * @var string
     */
    private $image_proxy_limit_protocol = '';

    /**
     * Tags and attribute whitelist.
     *
     * @var array
     */
    private $attribute_whitelist = array(
        'audio' => array('controls', 'src'),
        'video' => array('poster', 'controls', 'height', 'width', 'src'),
        'source' => array('src', 'type'),
        'dt' => array(),
        'dd' => array(),
        'dl' => array(),
        'table' => array(),
        'caption' => array(),
        'tr' => array(),
        'th' => array(),
        'td' => array(),
        'tbody' => array(),
        'thead' => array(),
        'h1' => array(),
        'h2' => array(),
        'h3' => array(),
        'h4' => array(),
        'h5' => array(),
        'h6' => array(),
        'strong' => array(),
        'em' => array(),
        'code' => array(),
        'pre' => array(),
        'blockquote' => array(),
        'p' => array(),
        'ul' => array(),
        'li' => array(),
        'ol' => array(),
        'br' => array(),
        'del' => array(),
        'a' => array('href'),
        'img' => array('src', 'title', 'alt'),
        'figure' => array(),
        'figcaption' => array(),
        'cite' => array(),
        'time' => array('datetime'),
        'abbr' => array('title'),
        'iframe' => array('width', 'height', 'frameborder', 'src', 'allowfullscreen'),
        'q' => array('cite'),
    );

    /**
     * Scheme whitelist.
     *
     * For a complete list go to http://en.wikipedia.org/wiki/URI_scheme
     *
     * @var array
     */
    private $scheme_whitelist = array(
        'bitcoin:',
        'callto:',
        'ed2k://',
        'facetime://',
        'feed:',
        'ftp://',
        'geo:',
        'git://',
        'http://',
        'https://',
        'irc://',
        'irc6://',
        'ircs://',
        'jabber:',
        'magnet:',
        'mailto:',
        'nntp://',
        'rtmp://',
        'sftp://',
        'sip:',
        'sips:',
        'skype:',
        'smb://',
        'sms:',
        'spotify:',
        'ssh:',
        'steam:',
        'svn://',
        'tel:',
    );

    /**
     * Iframe source whitelist, everything else is ignored.
     *
     * @var array
     */
    private $iframe_whitelist = array(
        'http://www.youtube.com',
        'https://www.youtube.com',
        'http://player.vimeo.com',
        'https://player.vimeo.com',
        'http://www.dailymotion.com',
        'https://www.dailymotion.com',
        'http://vk.com',
        'https://vk.com',
    );

    /**
     * Blacklisted resources.
     *
     * @var array
     */
    private $media_blacklist = array(
        'api.flattr.com',
        'feeds.feedburner.com',
        'share.feedsportal.com',
        'da.feedsportal.com',
        'rc.feedsportal.com',
        'rss.feedsportal.com',
        'res.feedsportal.com',
        'res1.feedsportal.com',
        'res2.feedsportal.com',
        'res3.feedsportal.com',
        'pi.feedsportal.com',
        'rss.nytimes.com',
        'feeds.wordpress.com',
        'stats.wordpress.com',
        'rss.cnn.com',
        'twitter.com/home?status=',
        'twitter.com/share',
        'twitter_icon_large.png',
        'www.facebook.com/sharer.php',
        'facebook_icon_large.png',
        'plus.google.com/share',
        'www.gstatic.com/images/icons/gplus-16.png',
        'www.gstatic.com/images/icons/gplus-32.png',
        'www.gstatic.com/images/icons/gplus-64.png',
    );

    /**
     * Attributes used for external resources.
     *
     * @var array
     */
    private $media_attributes = array(
        'src',
        'href',
        'poster',
    );

    /**
     * Attributes that must be integer.
     *
     * @var array
     */
    private $integer_attributes = array(
        'width',
        'height',
        'frameborder',
    );

    /**
     * Mandatory attributes for specified tags.
     *
     * @var array
     */
    private $required_attributes = array(
        'a' => array('href'),
        'img' => array('src'),
        'iframe' => array('src'),
        'audio' => array('src'),
        'source' => array('src'),
    );

    /**
     * Add attributes to specified tags.
     *
     * @var array
     */
    private $add_attributes = array(
        'a' => array('rel' => 'noreferrer', 'target' => '_blank'),
        'video' => array('controls' => 'true'),
    );

    /**
     * List of filters to apply.
     *
     * @var array
     */
    private $filters = array(
        'filterAllowedAttribute',
        'filterIntegerAttribute',
        'rewriteAbsoluteUrl',
        'filterIframeAttribute',
        'filterBlacklistResourceAttribute',
        'filterProtocolUrlAttribute',
        'rewriteImageProxyUrl',
        'secureIframeSrc',
        'removeYouTubeAutoplay',
    );

    /**
     * Add attributes to specified tags.
     *
     * @var \PicoFeed\Client\Url
     */
    private $website;

    /**
     * Constructor.
     *
     * @param \PicoFeed\Client\Url $website Website url instance
     */
    public function __construct(Url $website)
    {
        $this->website = $website;
    }

    /**
     * Apply filters to the attributes list.
     *
     * @param string $tag        Tag name
     * @param array  $attributes Attributes dictionary
     *
     * @return array Filtered attributes
     */
    public function filter($tag, array $attributes)
    {
        foreach ($attributes as $attribute => &$value) {
            foreach ($this->filters as $filter) {
                if (!$this->$filter($tag, $attribute, $value)) {
                    unset($attributes[$attribute]);
                    break;
                }
            }
        }

        return $attributes;
    }

    /**
     * Return true if the value is allowed (remove not allowed attributes).
     *
     * @param string $tag       Tag name
     * @param string $attribute Attribute name
     * @param string $value     Attribute value
     *
     * @return bool
     */
    public function filterAllowedAttribute($tag, $attribute, $value)
    {
        return isset($this->attribute_whitelist[$tag]) && in_array($attribute, $this->attribute_whitelist[$tag]);
    }

    /**
     * Return true if the value is not integer (remove attributes that should have an integer value).
     *
     * @param string $tag       Tag name
     * @param string $attribute Attribute name
     * @param string $value     Attribute value
     *
     * @return bool
     */
    public function filterIntegerAttribute($tag, $attribute, $value)
    {
        if (in_array($attribute, $this->integer_attributes)) {
            return ctype_digit($value);
        }

        return true;
    }

    /**
     * Return true if the iframe source is allowed (remove not allowed iframe).
     *
     * @param string $tag       Tag name
     * @param string $attribute Attribute name
     * @param string $value     Attribute value
     *
     * @return bool
     */
    public function filterIframeAttribute($tag, $attribute, $value)
    {
        if ($tag === 'iframe' && $attribute === 'src') {
            foreach ($this->iframe_whitelist as $url) {
                if (strpos($value, $url) === 0) {
                    return true;
                }
            }

            return false;
        }

        return true;
    }

    /**
     * Return true if the resource is not blacklisted (remove blacklisted resource attributes).
     *
     * @param string $tag       Tag name
     * @param string $attribute Attribute name
     * @param string $value     Attribute value
     *
     * @return bool
     */
    public function filterBlacklistResourceAttribute($tag, $attribute, $value)
    {
        if ($this->isResource($attribute) && $this->isBlacklistedMedia($value)) {
            return false;
        }

        return true;
    }

    /**
     * Convert all relative links to absolute url.
     *
     * @param string $tag       Tag name
     * @param string $attribute Attribute name
     * @param string $value     Attribute value
     *
     * @return bool
     */
    public function rewriteAbsoluteUrl($tag, $attribute, &$value)
    {
        if ($this->isResource($attribute)) {
            $value = Url::resolve($value, $this->website);
        }

        return true;
    }

    /**
     * Turns iframes' src attribute from http to https to prevent
     * mixed active content.
     *
     * @param string $tag       Tag name
     * @param array  $attribute Atttributes name
     * @param string $value     Attribute value
     *
     * @return bool
     */
    public function secureIframeSrc($tag, $attribute, &$value)
    {
        if ($tag === 'iframe' && $attribute === 'src' && strpos($value, 'http://') === 0) {
            $value = substr_replace($value, 's', 4, 0);
        }

        return true;
    }

    /**
     * Removes YouTube autoplay from iframes.
     *
     * @param string $tag       Tag name
     * @param array  $attribute Atttributes name
     * @param string $value     Attribute value
     *
     * @return bool
     */
    public function removeYouTubeAutoplay($tag, $attribute, &$value)
    {
        $regex = '%^(https://(?:www\.)?youtube.com/.*\?.*autoplay=)(1)(.*)%i';
        if ($tag === 'iframe' && $attribute === 'src' && preg_match($regex, $value)) {
            $value = preg_replace($regex, '${1}0$3', $value);
        }

        return true;
    }

    /**
     * Rewrite image url to use with a proxy.
     *
     * @param string $tag       Tag name
     * @param string $attribute Attribute name
     * @param string $value     Attribute value
     *
     * @return bool
     */
    public function rewriteImageProxyUrl($tag, $attribute, &$value)
    {
        if ($tag === 'img' && $attribute === 'src'
        && !($this->image_proxy_limit_protocol !== '' && stripos($value, $this->image_proxy_limit_protocol.':') !== 0)) {
            if ($this->image_proxy_url) {
                $value = sprintf($this->image_proxy_url, rawurlencode($value));
            } elseif (is_callable($this->image_proxy_callback)) {
                $value = call_user_func($this->image_proxy_callback, $value);
            }
        }

        return true;
    }

    /**
     * Return true if the scheme is authorized.
     *
     * @param string $tag       Tag name
     * @param string $attribute Attribute name
     * @param string $value     Attribute value
     *
     * @return bool
     */
    public function filterProtocolUrlAttribute($tag, $attribute, $value)
    {
        if ($this->isResource($attribute) && !$this->isAllowedProtocol($value)) {
            return false;
        }

        return true;
    }

    /**
     * Automatically add/override some attributes for specific tags.
     *
     * @param string $tag        Tag name
     * @param array  $attributes Attributes list
     *
     * @return array
     */
    public function addAttributes($tag, array $attributes)
    {
        if (isset($this->add_attributes[$tag])) {
            $attributes += $this->add_attributes[$tag];
        }

        return $attributes;
    }

    /**
     * Return true if all required attributes are present.
     *
     * @param string $tag        Tag name
     * @param array  $attributes Attributes list
     *
     * @return bool
     */
    public function hasRequiredAttributes($tag, array $attributes)
    {
        if (isset($this->required_attributes[$tag])) {
            foreach ($this->required_attributes[$tag] as $attribute) {
                if (!isset($attributes[$attribute])) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check if an attribute name is an external resource.
     *
     * @param string $attribute Attribute name
     *
     * @return bool
     */
    public function isResource($attribute)
    {
        return in_array($attribute, $this->media_attributes);
    }

    /**
     * Detect if the protocol is allowed or not.
     *
     * @param string $value Attribute value
     *
     * @return bool
     */
    public function isAllowedProtocol($value)
    {
        foreach ($this->scheme_whitelist as $protocol) {
            if (strpos($value, $protocol) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Detect if an url is blacklisted.
     *
     * @param string $resource Attribute value (URL)
     *
     * @return bool
     */
    public function isBlacklistedMedia($resource)
    {
        foreach ($this->media_blacklist as $name) {
            if (strpos($resource, $name) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Convert the attribute list to html.
     *
     * @param array $attributes Attributes
     *
     * @return string
     */
    public function toHtml(array $attributes)
    {
        $html = array();

        foreach ($attributes as $attribute => $value) {
            $html[] = sprintf('%s="%s"', $attribute, Filter::escape($value));
        }

        return implode(' ', $html);
    }

    /**
     * Set whitelisted tags and attributes for each tag.
     *
     * @param array $values List of tags: ['video' => ['src', 'cover'], 'img' => ['src']]
     *
     * @return Attribute
     */
    public function setWhitelistedAttributes(array $values)
    {
        $this->attribute_whitelist = $values ?: $this->attribute_whitelist;

        return $this;
    }

    /**
     * Set scheme whitelist.
     *
     * @param array $values List of scheme: ['http://', 'ftp://']
     *
     * @return Attribute
     */
    public function setSchemeWhitelist(array $values)
    {
        $this->scheme_whitelist = $values ?: $this->scheme_whitelist;

        return $this;
    }

    /**
     * Set media attributes (used to load external resources).
     *
     * @param array $values List of values: ['src', 'href']
     *
     * @return Attribute
     */
    public function setMediaAttributes(array $values)
    {
        $this->media_attributes = $values ?: $this->media_attributes;

        return $this;
    }

    /**
     * Set blacklisted external resources.
     *
     * @param array $values List of tags: ['http://google.com/', '...']
     *
     * @return Attribute
     */
    public function setMediaBlacklist(array $values)
    {
        $this->media_blacklist = $values ?: $this->media_blacklist;

        return $this;
    }

    /**
     * Set mandatory attributes for whitelisted tags.
     *
     * @param array $values List of tags: ['img' => 'src']
     *
     * @return Attribute
     */
    public function setRequiredAttributes(array $values)
    {
        $this->required_attributes = $values ?: $this->required_attributes;

        return $this;
    }

    /**
     * Set attributes to automatically to specific tags.
     *
     * @param array $values List of tags: ['a' => 'target="_blank"']
     *
     * @return Attribute
     */
    public function setAttributeOverrides(array $values)
    {
        $this->add_attributes = $values ?: $this->add_attributes;

        return $this;
    }

    /**
     * Set attributes that must be an integer.
     *
     * @param array $values List of tags: ['width', 'height']
     *
     * @return Attribute
     */
    public function setIntegerAttributes(array $values)
    {
        $this->integer_attributes = $values ?: $this->integer_attributes;

        return $this;
    }

    /**
     * Set allowed iframe resources.
     *
     * @param array $values List of tags: ['http://www.youtube.com']
     *
     * @return Attribute
     */
    public function setIframeWhitelist(array $values)
    {
        $this->iframe_whitelist = $values ?: $this->iframe_whitelist;

        return $this;
    }

    /**
     * Set image proxy URL.
     *
     * The original image url will be urlencoded
     *
     * @param string $url Proxy URL
     *
     * @return Attribute
     */
    public function setImageProxyUrl($url)
    {
        $this->image_proxy_url = $url ?: $this->image_proxy_url;

        return $this;
    }

    /**
     * Set image proxy callback.
     *
     * @param \Closure $callback
     *
     * @return Attribute
     */
    public function setImageProxyCallback($callback)
    {
        $this->image_proxy_callback = $callback ?: $this->image_proxy_callback;

        return $this;
    }

    /**
     * Set image proxy protocol restriction.
     *
     * @param string $value
     *
     * @return Attribute
     */
    public function setImageProxyProtocol($value)
    {
        $this->image_proxy_limit_protocol = $value ?: $this->image_proxy_limit_protocol;

        return $this;
    }
}
