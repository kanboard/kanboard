<?php

namespace Kanboard\Model;

use Kanboard\Event\TaskEvent;

/**
 * Task Creation
 *
 * @package  model
 * @author   Frederic Guillot
 */
class TaskCreation extends Base
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
        if (! $this->project->exists($values['project_id'])) {
            return 0;
        }

        $position = empty($values['position']) ? 0 : $values['position'];

        $this->prepare($values);
        $task_id = $this->persist(Task::TABLE, $values);

        if ($task_id !== false) {
            if ($position > 0 && $values['position'] > 1) {
                $this->taskPosition->movePosition($values['project_id'], $task_id, $values['column_id'], $position, $values['swimlane_id'], false);
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
        $this->helper->model->resetFields($values, array('date_started', 'creator_id', 'owner_id', 'swimlane_id', 'date_due', 'score', 'category_id', 'time_estimated'));

        if (empty($values['column_id'])) {
            $values['column_id'] = $this->column->getFirstColumnId($values['project_id']);
        }

        if (empty($values['color_id'])) {
            $values['color_id'] = $this->color->getDefaultColor();
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
        $values['position'] = $this->taskFinder->countByColumnAndSwimlaneId($values['project_id'], $values['column_id'], $values['swimlane_id']) + 1;
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

        $this->logger->debug('Event fired: '.Task::EVENT_CREATE_UPDATE);
        $this->logger->debug('Event fired: '.Task::EVENT_CREATE);

        $this->dispatcher->dispatch(Task::EVENT_CREATE_UPDATE, $event);
        $this->dispatcher->dispatch(Task::EVENT_CREATE, $event);

        if (! empty($values['description'])) {
            $this->userMention->fireEvents($values['description'], Task::EVENT_USER_MENTION, $event);
        }
    }
}
