<?php

namespace Kanboard\Api\Procedure;

/**
 * Group API controller
 *
 * @package  Kanboard\Api\Procedure
 * @author   Frederic Guillot
 */
class GroupProcedure extends BaseProcedure
{
    public function createGroup($name, $external_id = '')
    {
        return $this->groupModel->create($name, $external_id);
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

        return $this->groupModel->update($values);
    }

    public function removeGroup($group_id)
    {
        return $this->groupModel->remove($group_id);
    }

    public function getGroup($group_id)
    {
        return $this->groupModel->getById($group_id);
    }

    public function getAllGroups()
    {
        return $this->groupModel->getAll();
    }
}
