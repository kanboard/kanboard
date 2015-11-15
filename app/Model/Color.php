<?php

namespace Kanboard\Model;

/**
 * Color model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class Color extends Base
{
    /**
     * SQL table name for custom colors
     *
     * @var string
     */
    const TABLE = 'colors';

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
        'brown' => array(
            'name' => 'Brown',
            'background' => '#d7ccc8',
            'border' => '#4e342e',
        ),
        'deep_orange' => array(
            'name' => 'Deep Orange',
            'background' => '#ffab91',
            'border' => '#e64a19',
        ),
        'dark_grey' => array(
            'name' => 'Dark Grey',
            'background' => '#cfd8dc',
            'border' => '#455a64',
        ),
        'pink' => array(
            'name' => 'Pink',
            'background' => '#f48fb1',
            'border' => '#d81b60',
        ),
        'teal' => array(
            'name' => 'Teal',
            'background' => '#80cbc4',
            'border' => '#00695c',
        ),
        'cyan' => array(
            'name' => 'Cyan',
            'background' => '#b2ebf2',
            'border' => '#00bcd4',
        ),
        'lime' => array(
            'name' => 'Lime',
            'background' => '#e6ee9c',
            'border' => '#afb42b',
        ),
        'light_green' => array(
            'name' => 'Light Green',
            'background' => '#dcedc8',
            'border' => '#689f38',
        ),
        'amber' => array(
            'name' => 'Amber',
            'background' => '#ffe082',
            'border' => '#ffa000',
        ),
    );

    /**
     * Colors used in the app, since defaults can be overriden in the database.
     * Each color also contains bool 'is_usable' array key.
     *
     * @access private
     * @var array
     */
    private $colors = null;

    /**
     * Find a color id from the name or the id
     *
     * @access public
     * @param  string  $color
     * @return string
     */
    public function find($color)
    {
        if (!$this->colors) {
            $this->loadColors();
        }
        $color = strtolower($color);

        foreach ($this->colors as $color_id => $params) {
            if ($color_id === $color) {
                return $color_id;
            } elseif ($color === strtolower($params['name'])) {
                return $color_id;
            }
        }

        return '';
    }

    /**
     * Get color properties
     *
     * @access public
     * @param  string  $color_id
     * @return array
     */
    public function getColorProperties($color_id)
    {
        if (!$this->colors) {
            $this->loadColors();
        }
        if (isset($this->colors[$color_id])) {
            return $this->colors[$color_id];
        }

        return $this->colors[$this->getDefaultColor()];
    }

    /**
     * Get available colors
     *
     * @access public
     * @return array
     */
    public function getList($prepend = false)
    {
        if (!$this->colors) {
            $this->loadColors();
        }
        $listing = $prepend ? array('' => t('All colors')) : array();

        foreach ($this->colors as $color_id => $color) {
            if ($color['is_usable']) {
                $listing[$color_id] = t($color['name']);
            }
        }

        return $listing;
    }

    /**
     * Get the default color
     *
     * @access public
     * @return string
     */
    public function getDefaultColor()
    {
        return $this->config->get('default_color', 'yellow');
    }

    /**
     * Get the default colors
     *
     * @access public
     * @return array
     */
    public function getDefaultColors()
    {
        return $this->default_colors;
    }

    /**
     * Get the app colors
     *
     * @access public
     * @param  bool   $only_usable_colors  Wether return only a list of the usable colors or all of them (for control panel).
     * @return array
     */
    public function getColors($only_usable_colors = true)
    {
        if (!$this->colors) {
            $this->loadColors();
        }
        if (!$only_usable_colors) {
            return $this->colors;
        }
        $usable_colors = array();
        foreach ($this->colors as $color_id => $values) {
            if ($values['is_usable']) {
                $usable_colors[$color_id] = $values;
            }
        }
        return $usable_colors;
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
        $color = $this->getColorProperties($color_id);
        return $color['border'];
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
        $color = $this->getColorProperties($color_id);
        return $color['background'];
    }

    /**
     * Get CSS stylesheet of all colors
     *
     * @access public
     * @return string
     */
    public function getCss()
    {
        if (!$this->colors) {
            $this->loadColors();
        }
        $buffer = '';

        foreach ($this->colors as $color => $values) {
            $buffer .= 'div.color-'.$color.' {';
            $buffer .= 'background-color: '.$values['background'].';';
            $buffer .= 'border-color: '.$values['border'].' !important';
            $buffer .= '}';
            $buffer .= 'td.color-'.$color.' { background-color: '.$values['background'].'}';
        }

        return $buffer;
    }

    /**
     * Load colors from database, and if none exist, add default ones.
     *
     * @access private
     * @return void
     */
    private function loadColors()
    {
        if ($this->colors && !empty($this->colors)) {
            return;
        }

        $colors = $this->db->table(self::TABLE)->asc('position')->findAll();
        if (empty($colors)) {
            $this->populateTableWithDefaults();
            $colors = $this->db->table(self::TABLE)->asc('position')->findAll();
        }

        $this->colors = array();
        foreach ($colors as $color) {
            $color['is_usable'] = intval($color['is_usable']) > 0;
            $this->colors[$color['color_id']] = $color;
        }
    }

    /**
     * Store default colors in the database as default values.
     *
     * @access private
     * @return void
     */
    private function populateTableWithDefaults()
    {
        $position = 1;
        foreach ($this->default_colors as $color_id => $values) {
            $values['color_id'] = $color_id;
            $values['is_usable'] = 1;
            $values['position'] = $position;

            $this->db->table(self::TABLE)->save($values);

            $position++;
        }
    }

    /**
     * Update color data. The abormal expected format of the input
     * is due to the way HTML forms work in the framework, and this
     * method is used to process form at Settings / Custom colors.
     *
     * @access public
     * @param  array    $values
     */
    public function save(array $values)
    {
        if (!$this->colors) {
            $this->loadColors();
        }

        $results = array();
        $this->db->startTransaction();
        foreach ($this->default_colors as $color_id => $defval) {
            if (!isset($values[$color_id.'_name'])) {
                continue;
            }
            $color = array(
                'name' => $values[$color_id.'_name'],
                'is_usable' => isset($values[$color_id.'_is_usable']) ? 1 : 0,
                'background' => $values[$color_id.'_background'],
                'border' => $values[$color_id.'_border']
            );

            $results[] = $this->db->table(self::TABLE)->eq('color_id', $color_id)->save($color);
        }

        $this->db->closeTransaction();

        // Force a refetch.
        $this->colors = null;

        return ! in_array(false, $results, true);
    }
}
