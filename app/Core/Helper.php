<?php

namespace Kanboard\Core;

use Pimple\Container;

/**
 * Helper base class
 *
 * @package core
 * @author  Frederic Guillot
 *
 * @property \Kanboard\Helper\AppHelper               $app
 * @property \Kanboard\Helper\AssetHelper             $asset
 * @property \Kanboard\Helper\AvatarHelper            $avatar
 * @property \Kanboard\Helper\BoardHelper             $board
 * @property \Kanboard\Helper\CommentHelper           $comment
 * @property \Kanboard\Helper\DateHelper              $dt
 * @property \Kanboard\Helper\FileHelper              $file
 * @property \Kanboard\Helper\FormHelper              $form
 * @property \Kanboard\Helper\HookHelper              $hook
 * @property \Kanboard\Helper\ICalHelper              $ical
 * @property \Kanboard\Helper\ModalHelper             $modal
 * @property \Kanboard\Helper\ModelHelper             $model
 * @property \Kanboard\Helper\SubtaskHelper           $subtask
 * @property \Kanboard\Helper\TaskHelper              $task
 * @property \Kanboard\Helper\TextHelper              $text
 * @property \Kanboard\Helper\UrlHelper               $url
 * @property \Kanboard\Helper\UserHelper              $user
 * @property \Kanboard\Helper\LayoutHelper            $layout
 * @property \Kanboard\Helper\ProjectRoleHelper       $projectRole
 * @property \Kanboard\Helper\ProjectHeaderHelper     $projectHeader
 * @property \Kanboard\Helper\ProjectActivityHelper   $projectActivity
 * @property \Kanboard\Helper\MailHelper              $mail
 */
class Helper
{
    /**
     * Helper instances
     *
     * @access private
     * @var \Pimple\Container
     */
    private $helpers;

    /**
     * Container instance
     *
     * @access private
     * @var \Pimple\Container
     */
    private $container;

    /**
     * Constructor
     *
     * @access public
     * @param  \Pimple\Container   $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->helpers = new Container;
    }

    /**
     * Expose helpers with magic getter
     *
     * @access public
     * @param  string $helper
     * @return mixed
     */
    public function __get($helper)
    {
        return $this->getHelper($helper);
    }

    /**
     * Expose helpers with method
     *
     * @access public
     * @param  string $helper
     * @return mixed
     */
    public function getHelper($helper)
    {
        return $this->helpers[$helper];
    }

    /**
     * Register a new Helper
     *
     * @access public
     * @param  string $property
     * @param  string $className
     * @return Helper
     */
    public function register($property, $className)
    {
        $container = $this->container;

        $this->helpers[$property] = function() use ($className, $container) {
            return new $className($container);
        };

        return $this;
    }
}
