<?php

namespace Kanboard\Api;

/**
 * Link API controller
 *
 * @package  api
 * @author   Frederic Guillot
 */
class Link extends \Kanboard\Core\Base
{
    /**
     * Get a link by id
     *
     * @access public
     * @param  integer   $link_id   Link id
     * @return array
     */
    public function getLinkById($link_id)
    {
        return $this->link->getById($link_id);
    }

    /**
     * Get a link by name
     *
     * @access public
     * @param  string $label
     * @return array
     */
    public function getLinkByLabel($label)
    {
        return $this->link->getByLabel($label);
    }

    /**
     * Get the opposite link id
     *
     * @access public
     * @param  integer   $link_id   Link id
     * @return integer
     */
    public function getOppositeLinkId($link_id)
    {
        return $this->link->getOppositeLinkId($link_id);
    }

    /**
     * Get all links
     *
     * @access public
     * @return array
     */
    public function getAllLinks()
    {
        return $this->link->getAll();
    }

    /**
     * Create a new link label
     *
     * @access public
     * @param  string   $label
     * @param  string   $opposite_label
     * @return boolean|integer
     */
    public function createLink($label, $opposite_label = '')
    {
        $values = array(
            'label' => $label,
            'opposite_label' => $opposite_label,
        );

        list($valid, ) = $this->link->validateCreation($values);
        return $valid ? $this->link->create($label, $opposite_label) : false;
    }

    /**
     * Update a link
     *
     * @access public
     * @param  integer  $link_id
     * @param  integer  $opposite_link_id
     * @param  string   $label
     * @return boolean
     */
    public function updateLink($link_id, $opposite_link_id, $label)
    {
        $values = array(
            'id' => $link_id,
            'opposite_id' => $opposite_link_id,
            'label' => $label,
        );

        list($valid, ) = $this->link->validateModification($values);
        return $valid && $this->link->update($values);
    }

    /**
     * Remove a link a the relation to its opposite
     *
     * @access public
     * @param  integer  $link_id
     * @return boolean
     */
    public function removeLink($link_id)
    {
        return $this->link->remove($link_id);
    }
}
