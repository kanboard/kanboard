<?php

namespace Kanboard\Core\Ldap;

/**
 * LDAP Entry
 *
 * @package ldap
 * @author  Frederic Guillot
 */
class Entry
{
    /**
     * LDAP entry
     *
     * @access protected
     * @var array
     */
    protected $entry = array();

    /**
     * Constructor
     *
     * @access public
     * @param  array $entry
     */
    public function __construct(array $entry)
    {
        $this->entry = $entry;
    }

    /**
     * Get all attribute values
     *
     * @access public
     * @param  string  $attribute
     * @return string[]
     */
    public function getAll($attribute)
    {
        $attributes = array();

        if (! isset($this->entry[$attribute]['count'])) {
            return $attributes;
        }

        for ($i = 0; $i < $this->entry[$attribute]['count']; $i++) {
            $attributes[] = $this->entry[$attribute][$i];
        }

        return $attributes;
    }

    /**
     * Get first attribute value
     *
     * @access public
     * @param  string  $attribute
     * @param  string  $default
     * @return string
     */
    public function getFirstValue($attribute, $default = '')
    {
        return isset($this->entry[$attribute][0]) ? $this->entry[$attribute][0] : $default;
    }

    /**
     * Get entry distinguished name
     *
     * @access public
     * @return string
     */
    public function getDn()
    {
        return isset($this->entry['dn']) ? $this->entry['dn'] : '';
    }

    /**
     * Return true if the given value exists in attribute list
     *
     * @access public
     * @param  string  $attribute
     * @param  string  $value
     * @return boolean
     */
    public function hasValue($attribute, $value)
    {
        $attributes = $this->getAll($attribute);
        return in_array($value, $attributes);
    }
}
