<?php

namespace Kanboard\Import;

use Kanboard\Core\Base;
use Kanboard\Core\Csv;
use Kanboard\Core\ExternalLink\ExternalLinkManager;
use Kanboard\Core\ExternalLink\ExternalLinkProviderNotFound;
use SimpleValidator\Validator;
use SimpleValidator\Validators;

/**
 * Task CSV Import
 *
 * @package  Kanboard\Import
 * @author   Frederic Guillot
 */
class TaskImport extends Base
{
    protected $nbImportedTasks = 0;
    protected $projectId = 0;

    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;
        return $this;
    }

    public function getNumberOfImportedTasks()
    {
        return $this->nbImportedTasks;
    }

    public function getColumnMapping()
    {
        return array(
            'reference'         => e('Reference'),
            'title'             => e('Title'),
            'description'       => e('Description'),
            'assignee'          => e('Assignee Username'),
            'creator'           => e('Creator Username'),
            'color'             => e('Color Name'),
            'column'            => e('Column Name'),
            'category'          => e('Category Name'),
            'swimlane'          => e('Swimlane Name'),
            'score'             => e('Complexity'),
            'time_estimated'    => e('Time Estimated'),
            'time_spent'        => e('Time Spent'),
            'date_started'      => e('Start Date'),
            'date_due'          => e('Due Date'),
            'priority'          => e('Priority'),
            'is_active'         => e('Status'),
            'tags'              => e('Tags'),
            'external_link'     => e('External Link'),
        );
    }

    public function importTask(array $row, $lineNumber)
    {
        $task = $this->prepareTask($row);

        if ($this->validateCreation($task)) {
            $taskId = $this->taskCreationModel->create($task);

            if ($taskId > 0) {
                $this->logger->debug(__METHOD__.': imported successfully line '.$lineNumber);
                $this->nbImportedTasks++;

                if (! empty($row['tags'])) {
                    $this->taskTagModel->save($this->projectId, $taskId, explode_csv_field($row['tags']));
                }

                if (! empty($row['external_link'])) {
                    $this->createExternalLink($taskId, $row['external_link']);
                }
            } else {
                $this->logger->error(__METHOD__.': creation error at line '.$lineNumber);
            }
        } else {
            $this->logger->error(__METHOD__.': validation error at line '.$lineNumber);
        }
    }

    public function prepareTask(array $row)
    {
        $values = array();
        $values['project_id'] = $this->projectId;
        $values['reference'] = $row['reference'];
        $values['title'] = $row['title'];
        $values['description'] = $row['description'];
        $values['is_active'] = Csv::getBooleanValue($row['is_active']) == 1 ? 0 : 1;
        $values['score'] = (int) $row['score'];
        $values['priority'] = (int) $row['priority'];
        $values['time_estimated'] = (float) $row['time_estimated'];
        $values['time_spent'] = (float) $row['time_spent'];

        if (! empty($row['assignee'])) {
            $values['owner_id'] = $this->userModel->getIdByUsername($row['assignee']);
        }

        if (! empty($row['creator'])) {
            $values['creator_id'] = $this->userModel->getIdByUsername($row['creator']);
        }

        if (! empty($row['color'])) {
            $values['color_id'] = $this->colorModel->find($row['color']);
        }

        if (! empty($row['column'])) {
            $values['column_id'] = $this->columnModel->getColumnIdByTitle($this->projectId, $row['column']);
        }

        if (! empty($row['category'])) {
            $values['category_id'] = $this->categoryModel->getIdByName($this->projectId, $row['category']);
        }

        if (! empty($row['swimlane'])) {
            $values['swimlane_id'] = $this->swimlaneModel->getIdByName($this->projectId, $row['swimlane']);
        }

        if (! empty($row['date_due'])) {
            $values['date_due'] = $this->dateParser->getTimestamp($row['date_due']);
        }

        if (! empty($row['date_started'])) {
            $values['date_started'] = $this->dateParser->getTimestamp($row['date_started']);
        }

        $this->helper->model->removeEmptyFields(
            $values,
            array('owner_id', 'creator_id', 'color_id', 'column_id', 'category_id', 'swimlane_id', 'date_due', 'date_started', 'priority')
        );

        return $values;
    }

    protected function validateCreation(array $values)
    {
        $v = new Validator($values, array(
            new Validators\Integer('project_id', t('This value must be an integer')),
            new Validators\Required('project_id', t('The project is required')),
            new Validators\Required('title', t('The title is required')),
            new Validators\MaxLength('title', t('The maximum length is %d characters', 65535), 65535),
            new Validators\MaxLength('reference', t('The maximum length is %d characters', 255), 255),
        ));

        return $v->execute();
    }

    protected function createExternalLink($taskId, $externalLink)
    {
        try {
            $provider = $this->externalLinkManager
                ->setUserInputText($externalLink)
                ->setUserInputType(ExternalLinkManager::TYPE_AUTO)
                ->find();

            $link = $provider->getLink();
            $dependencies = $provider->getDependencies();
            $values = array(
                'task_id' => $taskId,
                'title' => $link->getTitle() ?: $link->getUrl(),
                'url' => $link->getUrl(),
                'link_type' => $provider->getType(),
                'dependency' => key($dependencies),
            );

            list($valid, $errors) = $this->externalLinkValidator->validateCreation($values);

            if ($valid) {
                $this->taskExternalLinkModel->create($values);
            } else {
                $this->logger->error(__METHOD__.': '.var_export($errors, true));
            }
        } catch (ExternalLinkProviderNotFound $e) {
            $this->logger->error(__METHOD__.': '.$e->getMessage());
        }
    }
}
