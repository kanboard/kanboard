<?php

namespace Kanboard\Model;

use Pimple\Container;
use Kanboard\Core\Base;

/**
 * Notification Type
 *
 * @package  model
 * @author   Frederic Guillot
 */
abstract class NotificationTypeModel extends Base
{
    /**
     * Container
     *
     * @access private
     * @var \Pimple\Container
     */
    private $classes;

    /**
     * Notification type labels
     *
     * @access private
     * @var array
     */
    private $labels = array();

    /**
     * Hidden notification types
     *
     * @access private
     * @var array
     */
    private $hiddens = array();

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
     * @param  boolean $hidden
     * @return NotificationTypeModel
     */
    public function setType($type, $label, $class, $hidden = false)
    {
        $container = $this->container;

        if ($hidden) {
            $this->hiddens[] = $type;
        } else {
            $this->labels[$type] = $label;
        }

        $this->classes[$type] = function () use ($class, $container) {
            return new $class($container);
        };

        return $this;
    }

    /**
     * Get mail notification type instance
     *
     * @access public
     * @param  string  $type
     * @return \Kanboard\Core\Notification\NotificationInterface
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

    /**
     * Get all hidden notification types
     *
     * @access public
     * @return array
     */
    public function getHiddenTypes()
    {
        return $this->hiddens;
    }

    /**
     * Keep only loaded notification types
     *
     * @access public
     * @param  string[] $types
     * @return array
     */
    public function filterTypes(array $types)
    {
        $classes = $this->classes;

        return array_filter($types, function ($type) use ($classes) {
            return isset($classes[$type]);
        });
    }
}
