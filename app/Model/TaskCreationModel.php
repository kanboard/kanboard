<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;

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
        $external_links = [];

        if (isset($values['tags'])) {
            $tags = $values['tags'];
            unset($values['tags']);
        }

        if (isset($values['links'])) {
            $external_links = $values['links'];
            unset($values['links']);
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

            if (! empty($external_links)) {
                foreach ($external_links as $external_link) {
                    $this->taskExternalLinkModel->create([
                        'task_id'       =>  $task_id,
                        'link_type'     =>  'weblink',
                        'dependency'    =>  'related',
                        'title'         =>  $external_link['title'],
                        'url'           =>  $external_link['url']
                    ]);
                }
            }

            $this->queueManager->push($this->taskEventJob->withParams(
                $task_id,
                array(TaskModel::EVENT_CREATE_UPDATE, TaskModel::EVENT_CREATE)
            ));
        }

        return (int) $task_id;
    }

    /**
     * Prepare data
     *
     * @access protected
     * @param  array    $values    Form values
     */
    protected function prepare(array &$values)
    {
        $values = $this->dateParser->convert($values, array('date_due'), true);
        $values = $this->dateParser->convert($values, array('date_started'), true);

        $this->helper->model->removeFields($values, array('another_task', 'duplicate_multiple_projects'));
        $this->helper->model->resetFields($values, array('creator_id', 'owner_id', 'date_due', 'date_started', 'score', 'category_id', 'time_estimated', 'time_spent'));

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

        $values['swimlane_id'] = empty($values['swimlane_id']) ? $this->swimlaneModel->getFirstActiveSwimlaneId($values['project_id']) : $values['swimlane_id'];
        $values['date_creation'] = time();
        $values['date_modification'] = $values['date_creation'];
        $values['date_moved'] = $values['date_creation'];
        $values['position'] = $this->taskFinderModel->countByColumnAndSwimlaneId($values['project_id'], $values['column_id'], $values['swimlane_id']) + 1;

        $this->hook->reference('model:task:creation:prepare', $values);
    }
}
