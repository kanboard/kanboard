<?php

namespace PicoFeed\Generator;

use PicoFeed\Base;
use PicoFeed\Parser\Item;

/**
 * File Content Generator
 *
 * @package PicoFeed\Generator
 * @author  Frederic Guillot
 */
class FileContentGenerator extends Base implements ContentGeneratorInterface
{
    private $extensions = array('pdf');

    /**
     * Execute Content Generator
     *
     * @access public
     * @param  Item $item
     * @return boolean
     */
    public function execute(Item $item)
    {
        foreach ($this->extensions as $extension) {
            if (substr($item->getUrl(), - strlen($extension)) === $extension) {
                $item->setContent('<a href="'.$item->getUrl().'" target="_blank">'.$item->getUrl().'</a>');
                return true;
            }
        }

        return false;
    }
}
