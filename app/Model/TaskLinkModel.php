<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;

/**
 * TaskLink model
 *
 * @package Kanboard\Model
 * @author  Olivier Maridat
 * @author  Frederic Guillot
 */
class TaskLinkModel extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'task_has_links';

    /**
     * Events
     *
     * @var string
     */
    const EVENT_CREATE_UPDATE = 'task_internal_link.create_update';
    const EVENT_DELETE        = 'task_internal_link.delete';

    /**
     * Get projectId from $task_link_id
     *
     * @access public
     * @param  integer $task_link_id
     * @return integer
     */
    public function getProjectId($task_link_id)
    {
        return $this->db
            ->table(self::TABLE)
            ->eq(self::TABLE.'.id', $task_link_id)
            ->join(TaskModel::TABLE, 'id', 'task_id')
            ->findOneColumn(TaskModel::TABLE . '.project_id') ?: 0;
    }

    /**
     * Get a task link
     *
     * @access public
     * @param  integer   $task_link_id   Task link id
     * @return array
     */
    public function getById($task_link_id)
    {
        return $this->db
            ->table(self::TABLE)
            ->columns(
                self::TABLE.'.id',
                self::TABLE.'.opposite_task_id',
                self::TABLE.'.task_id',
                self::TABLE.'.link_id',
                LinkModel::TABLE.'.label',
                LinkModel::TABLE.'.opposite_id AS opposite_link_id'
            )
            ->eq(self::TABLE.'.id', $task_link_id)
            ->join(LinkModel::TABLE, 'id', 'link_id')
            ->findOne();
    }

    /**
     * Get the opposite task link (use the unique index task_has_links_unique)
     *
     * @access public
     * @param  array     $task_link
     * @return array
     */
    public function getOppositeTaskLink(array $task_link)
    {
        $opposite_link_id = $this->linkModel->getOppositeLinkId($task_link['link_id']);

        return $this->db->table(self::TABLE)
                    ->eq('opposite_task_id', $task_link['task_id'])
                    ->eq('task_id', $task_link['opposite_task_id'])
                    ->eq('link_id', $opposite_link_id)
                    ->findOne();
    }

    /**
     * Get all links attached to a task
     *
     * @access public
     * @param  integer   $task_id   Task id
     * @return array
     */
    public function getAll($task_id)
    {
        return $this->db
                    ->table(self::TABLE)
                    ->columns(
                        self::TABLE.'.id',
                        self::TABLE.'.opposite_task_id AS task_id',
                        LinkModel::TABLE.'.label',
                        TaskModel::TABLE.'.title',
                        TaskModel::TABLE.'.is_active',
                        TaskModel::TABLE.'.project_id',
                        TaskModel::TABLE.'.column_id',
                        TaskModel::TABLE.'.color_id',
                        TaskModel::TABLE.'.time_spent AS task_time_spent',
                        TaskModel::TABLE.'.time_estimated AS task_time_estimated',
                        TaskModel::TABLE.'.owner_id AS task_assignee_id',
                        UserModel::TABLE.'.username AS task_assignee_username',
                        UserModel::TABLE.'.name AS task_assignee_name',
                        ColumnModel::TABLE.'.title AS column_title',
                        ProjectModel::TABLE.'.name AS project_name'
                    )
                    ->eq(self::TABLE.'.task_id', $task_id)
                    ->join(LinkModel::TABLE, 'id', 'link_id')
                    ->join(TaskModel::TABLE, 'id', 'opposite_task_id')
                    ->join(ColumnModel::TABLE, 'id', 'column_id', TaskModel::TABLE)
                    ->join(UserModel::TABLE, 'id', 'owner_id', TaskModel::TABLE)
                    ->join(ProjectModel::TABLE, 'id', 'project_id', TaskModel::TABLE)
                    ->asc(LinkModel::TABLE.'.id')
                    ->desc(ColumnModel::TABLE.'.position')
                    ->desc(TaskModel::TABLE.'.is_active')
                    ->asc(TaskModel::TABLE.'.position')
                    ->asc(TaskModel::TABLE.'.id')
                    ->findAll();
    }

    /**
     * Get all links attached to a task grouped by label
     *
     * @access public
     * @param  integer   $task_id   Task id
     * @return array
     */
    public function getAllGroupedByLabel($task_id)
    {
        $links = $this->getAll($task_id);
        $result = array();

        foreach ($links as $link) {
            if (! isset($result[$link['label']])) {
                $result[$link['label']] = array();
            }

            $result[$link['label']][] = $link;
        }

        return $result;
    }

    /**
     * Create a new link
     *
     * @access public
     * @param  integer   $task_id            Task id
     * @param  integer   $opposite_task_id   Opposite task id
     * @param  integer   $link_id            Link id
     * @return integer|boolean
     */
    public function create($task_id, $opposite_task_id, $link_id)
    {
        $this->db->startTransaction();

        $opposite_link_id = $this->linkModel->getOppositeLinkId($link_id);
        $task_link_id1 = $this->createTaskLink($task_id, $opposite_task_id, $link_id);
        $task_link_id2 = $this->createTaskLink($opposite_task_id, $task_id, $opposite_link_id);

        if ($task_link_id1 === false || $task_link_id2 === false) {
            $this->db->cancelTransaction();
            return false;
        }

        $this->db->closeTransaction();
        $this->fireEvents(array($task_link_id1, $task_link_id2), self::EVENT_CREATE_UPDATE);

        return $task_link_id1;
    }

    /**
     * Update a task link
     *
     * @access public
     * @param  integer   $task_link_id          Task link id
     * @param  integer   $task_id               Task id
     * @param  integer   $opposite_task_id      Opposite task id
     * @param  integer   $link_id               Link id
     * @return boolean
     */
    public function update($task_link_id, $task_id, $opposite_task_id, $link_id)
    {
        $this->db->startTransaction();

        $task_link = $this->getById($task_link_id);
        $opposite_task_link = $this->getOppositeTaskLink($task_link);
        $opposite_link_id = $this->linkModel->getOppositeLinkId($link_id);

        $result1 = $this->updateTaskLink($task_link_id, $task_id, $opposite_task_id, $link_id);
        $result2 = $this->updateTaskLink($opposite_task_link['id'], $opposite_task_id, $task_id, $opposite_link_id);

        if ($result1 === false || $result2 === false) {
            $this->db->cancelTransaction();
            return false;
        }

        $this->db->closeTransaction();
        $this->fireEvents(array($task_link_id, $opposite_task_link['id']), self::EVENT_CREATE_UPDATE);

        return true;
    }

    /**
     * Remove a link between two tasks
     *
     * @access public
     * @param  integer   $task_link_id
     * @return boolean
     */
    public function remove($task_link_id)
    {
        $this->taskLinkEventJob->execute($task_link_id, self::EVENT_DELETE);

        $this->db->startTransaction();

        $link = $this->getById($task_link_id);
        $link_id = $this->linkModel->getOppositeLinkId($link['link_id']);

        $result1 = $this->db
            ->table(self::TABLE)
            ->eq('id', $task_link_id)
            ->remove();

        $result2 = $this->db
            ->table(self::TABLE)
            ->eq('opposite_task_id', $link['task_id'])
            ->eq('task_id', $link['opposite_task_id'])
            ->eq('link_id', $link_id)
            ->remove();

        if ($result1 === false || $result2 === false) {
            $this->db->cancelTransaction();
            return false;
        }

        $this->db->closeTransaction();

        return true;
    }

    /**
     * Publish events
     *
     * @access protected
     * @param  integer[] $task_link_ids
     * @param  string    $eventName
     */
    protected function fireEvents(array $task_link_ids, $eventName)
    {
        foreach ($task_link_ids as $task_link_id) {
            $this->queueManager->push($this->taskLinkEventJob->withParams($task_link_id, $eventName));
        }
    }

    /**
     * Create task link
     *
     * @access protected
     * @param  integer $task_id
     * @param  integer $opposite_task_id
     * @param  integer $link_id
     * @return integer|boolean
     */
    protected function createTaskLink($task_id, $opposite_task_id, $link_id)
    {
        return $this->db->table(self::TABLE)->persist(array(
            'task_id'          => $task_id,
            'opposite_task_id' => $opposite_task_id,
            'link_id'          => $link_id,
        ));
    }

    /**
     * Update task link
     *
     * @access protected
     * @param  integer $task_link_id
     * @param  integer $task_id
     * @param  integer $opposite_task_id
     * @param  integer $link_id
     * @return boolean
     */
    protected function updateTaskLink($task_link_id, $task_id, $opposite_task_id, $link_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $task_link_id)->update(array(
            'task_id' => $task_id,
            'opposite_task_id' => $opposite_task_id,
            'link_id' => $link_id,
        ));
    }
}
