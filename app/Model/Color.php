<?php

namespace Model;

/**
 * Color model (TODO: model for the future color picker)
 *
 * @package  model
 * @author   Frederic Guillot
 */
class Color extends Base
{
    /**
     * Get available colors
     *
     * @access public
     * @return array
     */
    public function getList()
    {
        return array(
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
     * @param string $color Color String
     * @return string
     */
    public function getBorderColor($color){
        if ($color === 'blue') {
            return 'rgb(168, 207, 255)';
        } elseif ($color === 'purple') {
            return 'rgb(205, 133, 254)';
        } elseif ($color === 'grey') {
            return 'rgb(204, 204, 204)';
        } elseif ($color === 'red') {
            return 'rgb(255, 151, 151)';
        } elseif ($color === 'green') {
            return 'rgb(74, 227, 113)';
        } elseif ($color === 'yellow') {
            return 'rgb(223, 227, 45)';
        } elseif ($color === 'orange') {
            return 'rgb(255, 172, 98)';
        }
    }
    
    /**
     * Get Backgroundcolor from string
     *
     * @access public
     * @param string $color Color String
     * @return string
     */
    public function getBackgroundColor($color){
        if ($color === 'blue') {
            return 'rgb(219, 235, 255)';
        } elseif ($color === 'purple') {
            return 'rgb(223, 176, 255)';
        } elseif ($color === 'grey') {
            return 'rgb(238, 238, 238)';
        } elseif ($color === 'red') {
            return 'rgb(255, 187, 187)';
        } elseif ($color === 'green') {
            return 'rgb(189, 244, 203)';
        } elseif ($color === 'yellow') {
            return 'rgb(245, 247, 196)';
        } elseif ($color === 'orange') {
            return 'rgb(255, 215, 179)';
        } 
    }
    
    
}
