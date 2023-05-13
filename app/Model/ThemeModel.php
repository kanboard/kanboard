<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;

/**
 * Class Theme
 *
 * @package Kanboard\Model
 * @author  Frederic Guillot
 */
class ThemeModel extends Base
{
    /**
     * Get available theme
     *
     * @access public
     * @return array
     */
    public function getThemes()
    {
        return [
            'light' => t('Light theme'),
            'dark' => t('Dark theme'),
            'auto' => t('Automatic theme - Sync with system'),
        ];
    }
}
