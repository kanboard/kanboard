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
}
