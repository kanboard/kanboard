<?php

namespace Kanboard\Api;

/**
 * Group API controller
 *
 * @package  api
 * @author   Frederic Guillot
 */
class Group extends \Kanboard\Core\Base
{
    public function createGroup($name, $external_id = '')
    {
        return $this->group->create($name, $external_id);
    }

    public function updateGroup($group_id, $name = null, $external_id = null)
    {
        $values = array(
            'id' => $group_id,
            'name' => $name,
            'external_id' => $external_id,
        );

        foreach ($values as $key => $value) {
            if (is_null($value)) {
                unset($values[$key]);
            }
        }

        return $this->group->update($values);
    }

    public function removeGroup($group_id)
    {
        return $this->group->remove($group_id);
    }

    public function getGroup($group_id)
    {
        return $this->group->getById($group_id);
    }

    public function getAllGroups()
    {
        return $this->group->getAll();
    }
}
