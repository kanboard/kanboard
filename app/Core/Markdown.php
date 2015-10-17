<?php

namespace Kanboard\Core;

use Parsedown;
use Kanboard\Helper\Url;

/**
 * Specific Markdown rules for Kanboard
 *
 * @package core
 * @author  norcnorc
 * @author  Frederic Guillot
 */
class Markdown extends Parsedown
{
    private $link;
    private $helper;

    public function __construct($link, Url $helper)
    {
        $this->link = $link;
        $this->helper = $helper;
        $this->InlineTypes['#'][] = 'TaskLink';
        $this->inlineMarkerList .= '#';
    }

    protected function inlineTaskLink($Excerpt)
    {
        // Replace task #123 by a link to the task
        if (! empty($this->link) && preg_match('!#(\d+)!i', $Excerpt['text'], $matches)) {
            $url = $this->helper->href(
                $this->link['controller'],
                $this->link['action'],
                $this->link['params'] + array('task_id' => $matches[1])
            );

            return array(
                'extent' => strlen($matches[0]),
                'element' => array(
                    'name' => 'a',
                    'text' => $matches[0],
                    'attributes' => array('href' => $url)));
        }
    }
}
