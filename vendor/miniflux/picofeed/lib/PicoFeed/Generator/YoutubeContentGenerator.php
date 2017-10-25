<?php

namespace PicoFeed\Generator;

use PicoFeed\Base;
use PicoFeed\Parser\Item;

/**
 * Youtube Content Generator
 *
 * @package PicoFeed\Generator
 * @author  Frederic Guillot
 */
class YoutubeContentGenerator extends Base implements ContentGeneratorInterface
{
    /**
     * Execute Content Generator
     *
     * @access public
     * @param  Item $item
     * @return boolean
     */
    public function execute(Item $item)
    {
        if ($item->hasNamespace('yt')) {
            return $this->generateHtmlFromXml($item);
        }

        return $this->generateHtmlFromUrl($item);
    }

    /**
     * Generate HTML
     *
     * @access public
     * @param  Item $item
     * @return boolean
     */
    private function generateHtmlFromXml(Item $item)
    {
        $videoId = $item->getTag('yt:videoId');

        if (! empty($videoId)) {
            $item->setContent('<iframe width="560" height="315" src="//www.youtube.com/embed/'.$videoId[0].'" frameborder="0"></iframe>');
            return true;
        }

        return false;
    }

    /**
     * Generate HTML from item URL
     *
     * @access public
     * @param  Item $item
     * @return bool
     */
    public function generateHtmlFromUrl(Item $item)
    {
        if (preg_match('/youtube\.com\/watch\?v=(.*)/', $item->getUrl(), $matches)) {
            $item->setContent('<iframe width="560" height="315" src="//www.youtube.com/embed/'.$matches[1].'" frameborder="0"></iframe>');
            return true;
        }

        return false;
    }
}
