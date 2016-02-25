<?php

namespace Kanboard\Core;

use Parsedown;
use Pimple\Container;

/**
 * Specific Markdown rules for Kanboard
 *
 * @package core
 * @author  norcnorc
 * @author  Frederic Guillot
 */
class Markdown extends Parsedown
{
    /**
     * Link params for tasks
     *
     * @access private
     * @var array
     */
    private $link = array();

    /**
     * Container
     *
     * @access private
     * @var Container
     */
    private $container;

    /**
     * Constructor
     *
     * @access public
     * @param  Container  $container
     * @param  array      $link
     */
    public function __construct(Container $container, array $link)
    {
        $this->link = $link;
        $this->container = $container;
        $this->InlineTypes['#'][] = 'TaskLink';
        $this->InlineTypes['@'][] = 'UserLink';
        $this->inlineMarkerList .= '#@';
    }

    /**
     * Handle Task Links
     *
     * Replace "#123" by a link to the task
     *
     * @access public
     * @param  array  $Excerpt
     * @return array
     */
    protected function inlineTaskLink(array $Excerpt)
    {
        if (! empty($this->link) && preg_match('!#(\d+)!i', $Excerpt['text'], $matches)) {
            $url = $this->container['helper']->url->href(
                $this->link['controller'],
                $this->link['action'],
                $this->link['params'] + array('task_id' => $matches[1])
            );

            return array(
                'extent' => strlen($matches[0]),
                'element' => array(
                    'name' => 'a',
                    'text' => $matches[0],
                    'attributes' => array('href' => $url)
                ),
            );
        }
    }

    /**
     * Handle User Mentions
     *
     * Replace "@username" by a link to the user
     *
     * @access public
     * @param  array  $Excerpt
     * @return array
     */
    protected function inlineUserLink(array $Excerpt)
    {
        if (preg_match('/^@([^\s]+)/', $Excerpt['text'], $matches)) {
            $user_id = $this->container['user']->getIdByUsername($matches[1]);

            if (! empty($user_id)) {
                $url = $this->container['helper']->url->href('user', 'profile', array('user_id' => $user_id));

                return array(
                    'extent' => strlen($matches[0]),
                    'element' => array(
                        'name' => 'a',
                        'text' => $matches[0],
                        'attributes' => array('href' => $url, 'class' => 'user-mention-link'),
                    ),
                );
            }
        }
    }
}
