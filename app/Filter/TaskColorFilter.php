<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\ColorModel;
use Kanboard\Model\TaskModel;

/**
 * Filter tasks by color
 *
 * @package filter
 * @author  Frederic Guillot
 */
class TaskColorFilter extends BaseFilter implements FilterInterface
{
    /**
     * Color object
     *
     * @access private
     * @var    ColorModel
     */
    private $colorModel;

    /**
     * Set color model object
     *
     * @access public
     * @param  ColorModel $colorModel
     * @return TaskColorFilter
     */
    public function setColorModel(ColorModel $colorModel)
    {
        $this->colorModel = $colorModel;
        return $this;
    }

    /**
     * Get search attribute
     *
     * @access public
     * @return string[]
     */
    public function getAttributes()
    {
        return array('color', 'colour');
    }

    /**
     * Apply filter
     *
     * @access public
     * @return FilterInterface
     */
    public function apply()
    {
        $this->query->eq(TaskModel::TABLE.'.color_id', $this->colorModel->find($this->value));
        return $this;
    }
}
