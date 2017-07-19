<?php

namespace Kanboard\Import;

use Kanboard\Core\Base;
use Kanboard\Core\Csv;
use SimpleValidator\Validator;
use SimpleValidator\Validators;

/**
 * Task Import
 *
 * @package  import
 * @author   Frederic Guillot
 */
class TaskImport extends Base
{
    /**
     * Number of successful import
     *
     * @access public
     * @var integer
     */
    public $counter = 0;

    /**
     * Project id to import tasks
     *
     * @access public
     * @var integer
     */
    public $projectId;

    /**
     * Get mapping between CSV header and SQL columns
     *
     * @access public
     * @return array
     */
    public function getColumnMapping()
    {
        return array(
            'reference'         => 'Reference',
            'title'             => 'Title',
            'description'       => 'Description',
            'assignee'          => 'Assignee Username',
            'creator'           => 'Creator Username',
            'color'             => 'Color Name',
            'column'            => 'Column Name',
            'category'          => 'Category Name',
            'swimlane'          => 'Swimlane Name',
            'score'             => 'Complexity',
            'time_estimated'    => 'Time Estimated',
            'time_spent'        => 'Time Spent',
            'date_due'          => 'Due Date',
            'is_active'         => 'Closed',
        );
    }

    /**
     * Import a single row
     *
     * @access public
     * @param  array   $row
     * @param  integer $line_number
     */
    public function import(array $row, $line_number)
    {
        $row = $this->prepare($row);

        if ($this->validateCreation($row)) {
            if ($this->taskCreationModel->create($row) > 0) {
                $this->logger->debug('TaskImport: imported successfully line '.$line_number);
                $this->counter++;
            } else {
                $this->logger->error('TaskImport: creation error at line '.$line_number);
            }
        } else {
            $this->logger->error('TaskImport: validation error at line '.$line_number);
        }
    }

    /**
     * Format row before validation
     *
     * @access public
     * @param  array   $row
     * @return array
     */
    public function prepare(array $row)
    {
        $values = array();
        $values['project_id'] = $this->projectId;
        $values['reference'] = $row['reference'];
        $values['title'] = $row['title'];
        $values['description'] = $row['description'];
        $values['is_active'] = Csv::getBooleanValue($row['is_active']) == 1 ? 0 : 1;
        $values['score'] = (int) $row['score'];
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

        $this->helper->model->removeEmptyFields(
            $values,
            array('owner_id', 'creator_id', 'color_id', 'column_id', 'category_id', 'swimlane_id', 'date_due')
        );

        return $values;
    }

    /**
     * Validate user creation
     *
     * @access public
     * @param  array   $values
     * @return boolean
     */
    public function validateCreation(array $values)
    {
        $v = new Validator($values, array(
            new Validators\Integer('project_id', t('This value must be an integer')),
            new Validators\Required('project_id', t('The project is required')),
            new Validators\Required('title', t('The title is required')),
            new Validators\MaxLength('title', t('The maximum length is %d characters', 200), 200),
            new Validators\MaxLength('reference', t('The maximum length is %d characters', 50), 50),
        ));

        return $v->execute();
    }
}
