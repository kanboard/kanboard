<?php

namespace Model;

/**
 * Color model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class Color extends Base
{
    /**
     * Default colors
     *
     * @access private
     * @var array
     */
    private $default_colors = array(
        'yellow' => array(
            'name' => 'Yellow',
            'background' => 'rgb(245, 247, 196)',
            'border' => 'rgb(223, 227, 45)',
        ),
        'blue' => array(
            'name' => 'Blue',
            'background' => 'rgb(219, 235, 255)',
            'border' => 'rgb(168, 207, 255)',
        ),
        'green' => array(
            'name' => 'Green',
            'background' => 'rgb(189, 244, 203)',
            'border' => 'rgb(74, 227, 113)',
        ),
        'purple' => array(
            'name' => 'Purple',
            'background' => 'rgb(223, 176, 255)',
            'border' => 'rgb(205, 133, 254)',
        ),
        'red' => array(
            'name' => 'Red',
            'background' => 'rgb(255, 187, 187)',
            'border' => 'rgb(255, 151, 151)',
        ),
        'orange' => array(
            'name' => 'Orange',
            'background' => 'rgb(255, 215, 179)',
            'border' => 'rgb(255, 172, 98)',
        ),
        'grey' => array(
            'name' => 'Grey',
            'background' => 'rgb(238, 238, 238)',
            'border' => 'rgb(204, 204, 204)',
        ),
    );

    /**
     * Get available colors
     *
     * @access public
     * @return array
     */
    public function getList($prepend = false)
    {
        $listing = $prepend ? array('' => t('All colors')) : array();

        return $listing + array(
            'yellow' => t('Yellow'),
            'blue' => t('Blue'),
            'green' => t('Green'),
            'purple' => t('Purple'),
            'red' => t('Red'),
            'orange' => t('Orange'),
            'grey' => t('Grey'),
        );
    }

    /**
     * Get the default color
     *
     * @access public
     * @return string
     */
    public function getDefaultColor()
    {
        return 'yellow'; // TODO: make this parameter configurable
    }

    /**
     * Get Bordercolor from string
     *
     * @access public
     * @param  string   $color_id   Color id
     * @return string
     */
    public function getBorderColor($color_id)
    {
        if (isset($this->default_colors[$color_id])) {
            return $this->default_colors[$color_id]['border'];
        }

        return $this->default_colors[$this->getDefaultColor()]['border'];
    }

    /**
     * Get background color from the color_id
     *
     * @access public
     * @param  string   $color_id   Color id
     * @return string
     */
    public function getBackgroundColor($color_id)
    {
        if (isset($this->default_colors[$color_id])) {
            return $this->default_colors[$color_id]['background'];
        }

        return $this->default_colors[$this->getDefaultColor()]['background'];
    }

    /**
     * Get CSS stylesheet of all colors
     *
     * @access public
     * @return string
     */
    public function getCss()
    {
        $buffer = '';

        foreach ($this->default_colors as $color => $values) {
            $buffer .= 'td.color-'.$color.',';
            $buffer .= 'div.color-'.$color.' {';
            $buffer .= 'background-color: '.$values['background'].';';
            $buffer .= 'border-color: '.$values['border'];
            $buffer .= '}';
        }

        return $buffer;
    }
}
