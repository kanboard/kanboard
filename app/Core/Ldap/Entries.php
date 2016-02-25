<?php

namespace Kanboard\Core\Ldap;

/**
 * LDAP Entries
 *
 * @package ldap
 * @author  Frederic Guillot
 */
class Entries
{
    /**
     * LDAP entries
     *
     * @access protected
     * @var array
     */
    protected $entries = array();

    /**
     * Constructor
     *
     * @access public
     * @param  array $entries
     */
    public function __construct(array $entries)
    {
        $this->entries = $entries;
    }

    /**
     * Get all entries
     *
     * @access public
     * @return Entry[]
     */
    public function getAll()
    {
        $entities = array();

        if (! isset($this->entries['count'])) {
            return $entities;
        }

        for ($i = 0; $i < $this->entries['count']; $i++) {
            $entities[] = new Entry($this->entries[$i]);
        }

        return $entities;
    }

    /**
     * Get first entry
     *
     * @access public
     * @return Entry
     */
    public function getFirstEntry()
    {
        return new Entry(isset($this->entries[0]) ? $this->entries[0] : array());
    }
}
