<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;
use Kanboard\Event\TaskEvent;

/**
 * Task Creation
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class TaskCreationModel extends Base
{
    /**
     * Create a task
     *
     * @access public
     * @param  array    $values   Form values
     * @return integer
     */
    public function create(array $values)
    {
        $position = empty($values['position']) ? 0 : $values['position'];
        $tags = array();

        if (isset($values['tags'])) {
            $tags = $values['tags'];
            unset($values['tags']);
        }

        $this->prepare($values);
        $task_id = $this->db->table(TaskModel::TABLE)->persist($values);

        if ($task_id !== false) {
            if ($position > 0 && $values['position'] > 1) {
                $this->taskPositionModel->movePosition($values['project_id'], $task_id, $values['column_id'], $position, $values['swimlane_id'], false);
            }

            if (! empty($tags)) {
                $this->taskTagModel->save($values['project_id'], $task_id, $tags);
            }

            $this->fireEvents($task_id, $values);
        }

        return (int) $task_id;
    }

    /**
     * Prepare data
     *
     * @access public
     * @param  array    $values    Form values
     */
    public function prepare(array &$values)
    {
        $values = $this->dateParser->convert($values, array('date_due'));
        $values = $this->dateParser->convert($values, array('date_started'), true);

        $this->helper->model->removeFields($values, array('another_task'));
        $this->helper->model->resetFields($values, array('creator_id', 'owner_id', 'swimlane_id', 'date_due', 'date_started', 'score', 'category_id', 'time_estimated', 'time_spent'));

        if (empty($values['column_id'])) {
            $values['column_id'] = $this->columnModel->getFirstColumnId($values['project_id']);
        }

        if (empty($values['color_id'])) {
            $values['color_id'] = $this->colorModel->getDefaultColor();
        }

        if (empty($values['title'])) {
            $values['title'] = t('Untitled');
        }

        if ($this->userSession->isLogged()) {
            $values['creator_id'] = $this->userSession->getId();
        }

        $values['swimlane_id'] = empty($values['swimlane_id']) ? 0 : $values['swimlane_id'];
        $values['date_creation'] = time();
        $values['date_modification'] = $values['date_creation'];
        $values['date_moved'] = $values['date_creation'];
        $values['position'] = $this->taskFinderModel->countByColumnAndSwimlaneId($values['project_id'], $values['column_id'], $values['swimlane_id']) + 1;
    }

    /**
     * Fire events
     *
     * @access private
     * @param  integer  $task_id     Task id
     * @param  array    $values      Form values
     */
    private function fireEvents($task_id, array $values)
    {
        $event = new TaskEvent(array('task_id' => $task_id) + $values);

        $this->logger->debug('Event fired: '.TaskModel::EVENT_CREATE_UPDATE);
        $this->logger->debug('Event fired: '.TaskModel::EVENT_CREATE);

        $this->dispatcher->dispatch(TaskModel::EVENT_CREATE_UPDATE, $event);
        $this->dispatcher->dispatch(TaskModel::EVENT_CREATE, $event);

        if (! empty($values['description'])) {
            $this->userMentionModel->fireEvents($values['description'], TaskModel::EVENT_USER_MENTION, $event);
        }
    }
}
