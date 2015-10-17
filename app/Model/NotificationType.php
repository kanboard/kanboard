<?php

namespace Kanboard\Model;

use Pimple\Container;

/**
 * Notification Type
 *
 * @package  model
 * @author   Frederic Guillot
 */
abstract class NotificationType extends Base
{
    /**
     * Mail transport instances
     *
     * @access private
     * @var \Pimple\Container
     */
    private $classes;

    /**
     * Mail transport instances
     *
     * @access private
     * @var array
     */
    private $labels = array();

    /**
     * Constructor
     *
     * @access public
     * @param  \Pimple\Container   $container
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->classes = new Container;
    }

    /**
     * Add a new notification type
     *
     * @access public
     * @param  string  $type
     * @param  string  $label
     * @param  string  $class
     */
    public function setType($type, $label, $class)
    {
        $container = $this->container;
        $this->labels[$type] = $label;

        $this->classes[$type] = function () use ($class, $container) {
            return new $class($container);
        };
    }

    /**
     * Get mail notification type instance
     *
     * @access public
     * @param  string  $type
     * @return NotificationInterface
     */
    public function getType($type)
    {
        return $this->classes[$type];
    }

    /**
     * Get all notification types with labels
     *
     * @access public
     * @return array
     */
    public function getTypes()
    {
        return $this->labels;
    }
}
