<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;

class PredefinedTaskDescriptionModel extends Base
{
    const TABLE = 'predefined_task_descriptions';

    public function getAll($projectId)
    {
        return $this->db->table(self::TABLE)->eq('project_id', $projectId)->findAll();
    }

    public function getList($projectId)
    {
        return array('' => t('None')) + $this->db->hashtable(self::TABLE)->eq('project_id', $projectId)->getAll('id', 'title');
    }

    public function getById($projectId, $id)
    {
        return $this->db->table(self::TABLE)->eq('project_id', $projectId)->eq('id', $id)->findOne();
    }

    public function getDescriptionById($projectId, $id)
    {
        return $this->db->table(self::TABLE)->eq('project_id', $projectId)->eq('id', $id)->findOneColumn('description');
    }

    public function create($projectId, $title, $description)
    {
        return $this->db->table(self::TABLE)->persist(array(
            'project_id' => $projectId,
            'title' => $title,
            'description' => $description,
        ));
    }

    public function update($projectId, $id, $title, $description)
    {
        return $this->db->table(self::TABLE)->eq('project_id', $projectId)->eq('id', $id)->update(array(
            'title' => $title,
            'description' => $description,
        ));
    }

    public function remove($projectId, $id)
    {
        return $this->db->table(self::TABLE)->eq('project_id', $projectId)->eq('id', $id)->remove();
    }
}
