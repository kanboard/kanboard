<?php

namespace Kanboard\Model;

/**
 * Task Filter
 *
 * @package  model
 * @author   Frederic Guillot
 */
class TaskFilter extends Base
{
    /**
     * Filters mapping
     *
     * @access private
     * @var array
     */
    private $filters = array(
        'T_ASSIGNEE' => 'filterByAssignee',
        'T_COLOR' => 'filterByColors',
        'T_DUE' => 'filterByDueDate',
        'T_UPDATED' => 'filterByModificationDate',
        'T_CREATED' => 'filterByCreationDate',
        'T_TITLE' => 'filterByTitle',
        'T_STATUS' => 'filterByStatusName',
        'T_DESCRIPTION' => 'filterByDescription',
        'T_CATEGORY' => 'filterByCategoryName',
        'T_PROJECT' => 'filterByProjectName',
        'T_COLUMN' => 'filterByColumnName',
        'T_REFERENCE' => 'filterByReference',
        'T_SWIMLANE' => 'filterBySwimlaneName',
    );

    /**
     * Query
     *
     * @access public
     * @var \PicoDb\Table
     */
    public $query;

    /**
     * Apply filters according to the search input
     *
     * @access public
     * @param  string   $input
     * @return TaskFilter
     */
    public function search($input)
    {
        $tree = $this->lexer->map($this->lexer->tokenize($input));
        $this->query = $this->taskFinder->getExtendedQuery();

        if (empty($tree)) {
            $this->filterByTitle($input);
        }

        foreach ($tree as $filter => $value) {
            $method = $this->filters[$filter];
            $this->$method($value);
        }

        return $this;
    }

    /**
     * Create a new query
     *
     * @access public
     * @return TaskFilter
     */
    public function create()
    {
        $this->query = $this->db->table(Task::TABLE);
        $this->query->left(User::TABLE, 'ua', 'id', Task::TABLE, 'owner_id');
        $this->query->left(User::TABLE, 'uc', 'id', Task::TABLE, 'creator_id');

        $this->query->columns(
            Task::TABLE.'.*',
            'ua.email AS assignee_email',
            'ua.name AS assignee_name',
            'ua.username AS assignee_username',
            'uc.email AS creator_email',
            'uc.username AS creator_username'
        );

        return $this;
    }

    /**
     * Create a new subtask query
     *
     * @access public
     * @return \PicoDb\Table
     */
    public function createSubtaskQuery()
    {
        return $this->db->table(Subtask::TABLE)
            ->columns(
                Subtask::TABLE.'.user_id',
                Subtask::TABLE.'.task_id',
                User::TABLE.'.name',
                User::TABLE.'.username'
            )
            ->join(User::TABLE, 'id', 'user_id', Subtask::TABLE)
            ->neq(Subtask::TABLE.'.status', Subtask::STATUS_DONE);
    }

    /**
     * Clone the filter
     *
     * @access public
     * @return TaskFilter
     */
    public function copy()
    {
        $filter = new static($this->container);
        $filter->query = clone($this->query);
        $filter->query->condition = clone($this->query->condition);
        return $filter;
    }

    /**
     * Exclude a list of task_id
     *
     * @access public
     * @param  integer[]  $task_ids
     * @return TaskFilter
     */
    public function excludeTasks(array $task_ids)
    {
        $this->query->notin(Task::TABLE.'.id', $task_ids);
        return $this;
    }

    /**
     * Filter by id
     *
     * @access public
     * @param  integer  $task_id
     * @return TaskFilter
     */
    public function filterById($task_id)
    {
        if ($task_id > 0) {
            $this->query->eq(Task::TABLE.'.id', $task_id);
        }

        return $this;
    }

    /**
     * Filter by reference
     *
     * @access public
     * @param  string  $reference
     * @return TaskFilter
     */
    public function filterByReference($reference)
    {
        if (! empty($reference)) {
            $this->query->eq(Task::TABLE.'.reference', $reference);
        }

        return $this;
    }

    /**
     * Filter by title
     *
     * @access public
     * @param  string  $title
     * @return TaskFilter
     */
    public function filterByDescription($title)
    {
        $this->query->ilike(Task::TABLE.'.description', '%'.$title.'%');
        return $this;
    }

    /**
     * Filter by title or id if the string is like #123 or an integer
     *
     * @access public
     * @param  string  $title
     * @return TaskFilter
     */
    public function filterByTitle($title)
    {
        if (ctype_digit($title) || (strlen($title) > 1 && $title{0} === '#' && ctype_digit(substr($title, 1)))) {
            $this->query->beginOr();
            $this->query->eq(Task::TABLE.'.id', str_replace('#', '', $title));
            $this->query->ilike(Task::TABLE.'.title', '%'.$title.'%');
            $this->query->closeOr();
        } else {
            $this->query->ilike(Task::TABLE.'.title', '%'.$title.'%');
        }

        return $this;
    }

    /**
     * Filter by a list of project id
     *
     * @access public
     * @param  array  $project_ids
     * @return TaskFilter
     */
    public function filterByProjects(array $project_ids)
    {
        $this->query->in(Task::TABLE.'.project_id', $project_ids);
        return $this;
    }

    /**
     * Filter by project id
     *
     * @access public
     * @param  integer  $project_id
     * @return TaskFilter
     */
    public function filterByProject($project_id)
    {
        if ($project_id > 0) {
            $this->query->eq(Task::TABLE.'.project_id', $project_id);
        }

        return $this;
    }

    /**
     * Filter by project name
     *
     * @access public
     * @param  array    $values   List of project name
     * @return TaskFilter
     */
    public function filterByProjectName(array $values)
    {
        $this->query->beginOr();

        foreach ($values as $project) {
            if (ctype_digit($project)) {
                $this->query->eq(Task::TABLE.'.project_id', $project);
            } else {
                $this->query->ilike(Project::TABLE.'.name', $project);
            }
        }

        $this->query->closeOr();
    }

    /**
     * Filter by swimlane name
     *
     * @access public
     * @param  array    $values   List of swimlane name
     * @return TaskFilter
     */
    public function filterBySwimlaneName(array $values)
    {
        $this->query->beginOr();

        foreach ($values as $swimlane) {
            if ($swimlane === 'default') {
                $this->query->eq(Task::TABLE.'.swimlane_id', 0);
            } else {
                $this->query->ilike(Swimlane::TABLE.'.name', $swimlane);
                $this->query->addCondition(Task::TABLE.'.swimlane_id=0 AND '.Project::TABLE.'.default_swimlane '.$this->db->getDriver()->getOperator('ILIKE')." '$swimlane'");
            }
        }

        $this->query->closeOr();
    }

    /**
     * Filter by category id
     *
     * @access public
     * @param  integer  $category_id
     * @return TaskFilter
     */
    public function filterByCategory($category_id)
    {
        if ($category_id >= 0) {
            $this->query->eq(Task::TABLE.'.category_id', $category_id);
        }

        return $this;
    }

    /**
     * Filter by category
     *
     * @access public
     * @param  array    $values   List of assignees
     * @return TaskFilter
     */
    public function filterByCategoryName(array $values)
    {
        $this->query->beginOr();

        foreach ($values as $category) {
            if ($category === 'none') {
                $this->query->eq(Task::TABLE.'.category_id', 0);
            } else {
                $this->query->eq(Category::TABLE.'.name', $category);
            }
        }

        $this->query->closeOr();
    }

    /**
     * Filter by assignee
     *
     * @access public
     * @param  integer  $owner_id
     * @return TaskFilter
     */
    public function filterByOwner($owner_id)
    {
        if ($owner_id >= 0) {
            $this->query->eq(Task::TABLE.'.owner_id', $owner_id);
        }

        return $this;
    }

    /**
     * Filter by assignee names
     *
     * @access public
     * @param  array    $values   List of assignees
     * @return TaskFilter
     */
    public function filterByAssignee(array $values)
    {
        $this->query->beginOr();

        foreach ($values as $assignee) {
            switch ($assignee) {
                case 'me':
                    $this->query->eq(Task::TABLE.'.owner_id', $this->userSession->getId());
                    break;
                case 'nobody':
                    $this->query->eq(Task::TABLE.'.owner_id', 0);
                    break;
                default:
                    $this->query->ilike(User::TABLE.'.username', '%'.$assignee.'%');
                    $this->query->ilike(User::TABLE.'.name', '%'.$assignee.'%');
            }
        }

        $this->filterBySubtaskAssignee($values);

        $this->query->closeOr();

        return $this;
    }

    /**
     * Filter by subtask assignee names
     *
     * @access public
     * @param  array    $values   List of assignees
     * @return TaskFilter
     */
    public function filterBySubtaskAssignee(array $values)
    {
        $subtaskQuery = $this->createSubtaskQuery();
        $subtaskQuery->beginOr();

        foreach ($values as $assignee) {
            if ($assignee === 'me') {
                $subtaskQuery->eq(Subtask::TABLE.'.user_id', $this->userSession->getId());
            } else {
                $subtaskQuery->ilike(User::TABLE.'.username', '%'.$assignee.'%');
                $subtaskQuery->ilike(User::TABLE.'.name', '%'.$assignee.'%');
            }
        }

        $subtaskQuery->closeOr();

        $this->query->in(Task::TABLE.'.id', $subtaskQuery->findAllByColumn('task_id'));

        return $this;
    }

    /**
     * Filter by color
     *
     * @access public
     * @param  string  $color_id
     * @return TaskFilter
     */
    public function filterByColor($color_id)
    {
        if ($color_id !== '') {
            $this->query->eq(Task::TABLE.'.color_id', $color_id);
        }

        return $this;
    }

    /**
     * Filter by colors
     *
     * @access public
     * @param  array   $colors
     * @return TaskFilter
     */
    public function filterByColors(array $colors)
    {
        $this->query->beginOr();

        foreach ($colors as $color) {
            $this->filterByColor($this->color->find($color));
        }

        $this->query->closeOr();

        return $this;
    }

    /**
     * Filter by column
     *
     * @access public
     * @param  integer $column_id
     * @return TaskFilter
     */
    public function filterByColumn($column_id)
    {
        if ($column_id >= 0) {
            $this->query->eq(Task::TABLE.'.column_id', $column_id);
        }

        return $this;
    }

    /**
     * Filter by column name
     *
     * @access public
     * @param  array    $values   List of column name
     * @return TaskFilter
     */
    public function filterByColumnName(array $values)
    {
        $this->query->beginOr();

        foreach ($values as $project) {
            $this->query->ilike(Board::TABLE.'.title', $project);
        }

        $this->query->closeOr();
    }

    /**
     * Filter by swimlane
     *
     * @access public
     * @param  integer  $swimlane_id
     * @return TaskFilter
     */
    public function filterBySwimlane($swimlane_id)
    {
        if ($swimlane_id >= 0) {
            $this->query->eq(Task::TABLE.'.swimlane_id', $swimlane_id);
        }

        return $this;
    }

    /**
     * Filter by status name
     *
     * @access public
     * @param  string  $status
     * @return TaskFilter
     */
    public function filterByStatusName($status)
    {
        if ($status === 'open' || $status === 'closed') {
            $this->filterByStatus($status === 'open' ? Task::STATUS_OPEN : Task::STATUS_CLOSED);
        }

        return $this;
    }

    /**
     * Filter by status
     *
     * @access public
     * @param  integer  $is_active
     * @return TaskFilter
     */
    public function filterByStatus($is_active)
    {
        if ($is_active >= 0) {
            $this->query->eq(Task::TABLE.'.is_active', $is_active);
        }

        return $this;
    }

    /**
     * Filter by due date
     *
     * @access public
     * @param  string      $date      ISO8601 date format
     * @return TaskFilter
     */
    public function filterByDueDate($date)
    {
        $this->query->neq(Task::TABLE.'.date_due', 0);
        $this->query->notNull(Task::TABLE.'.date_due');
        return $this->filterWithOperator(Task::TABLE.'.date_due', $date, true);
    }

    /**
     * Filter by due date (range)
     *
     * @access public
     * @param  string  $start
     * @param  string  $end
     * @return TaskFilter
     */
    public function filterByDueDateRange($start, $end)
    {
        $this->query->gte('date_due', $this->dateParser->getTimestampFromIsoFormat($start));
        $this->query->lte('date_due', $this->dateParser->getTimestampFromIsoFormat($end));

        return $this;
    }

    /**
     * Filter by start date (range)
     *
     * @access public
     * @param  string  $start
     * @param  string  $end
     * @return TaskFilter
     */
    public function filterByStartDateRange($start, $end)
    {
        $this->query->addCondition($this->getCalendarCondition(
            $this->dateParser->getTimestampFromIsoFormat($start),
            $this->dateParser->getTimestampFromIsoFormat($end),
            'date_started',
            'date_completed'
        ));

        return $this;
    }

    /**
     * Filter by creation date
     *
     * @access public
     * @param  string      $date      ISO8601 date format
     * @return TaskFilter
     */
    public function filterByCreationDate($date)
    {
        if ($date === 'recently') {
            return $this->filterRecentlyDate(Task::TABLE.'.date_creation');
        }

        return $this->filterWithOperator(Task::TABLE.'.date_creation', $date, true);
    }

    /**
     * Filter by creation date
     *
     * @access public
     * @param  string  $start
     * @param  string  $end
     * @return TaskFilter
     */
    public function filterByCreationDateRange($start, $end)
    {
        $this->query->addCondition($this->getCalendarCondition(
            $this->dateParser->getTimestampFromIsoFormat($start),
            $this->dateParser->getTimestampFromIsoFormat($end),
            'date_creation',
            'date_completed'
        ));

        return $this;
    }

    /**
     * Filter by modification date
     *
     * @access public
     * @param  string      $date      ISO8601 date format
     * @return TaskFilter
     */
    public function filterByModificationDate($date)
    {
        if ($date === 'recently') {
            return $this->filterRecentlyDate(Task::TABLE.'.date_modification');
        }

        return $this->filterWithOperator(Task::TABLE.'.date_modification', $date, true);
    }

    /**
     * Get all results of the filter
     *
     * @access public
     * @return array
     */
    public function findAll()
    {
        return $this->query->asc(Task::TABLE.'.id')->findAll();
    }

    /**
     * Get the PicoDb query
     *
     * @access public
     * @return \PicoDb\Table
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Get swimlanes and tasks to display the board
     *
     * @access public
     * @return array
     */
    public function getBoard($project_id)
    {
        $tasks = $this->filterByProject($project_id)->query->asc(Task::TABLE.'.position')->findAll();

        return $this->board->getBoard($project_id, function ($project_id, $column_id, $swimlane_id) use ($tasks) {
            return array_filter($tasks, function (array $task) use ($column_id, $swimlane_id) {
                return $task['column_id'] == $column_id && $task['swimlane_id'] == $swimlane_id;
            });
        });
    }

    /**
     * Filter with an operator
     *
     * @access public
     * @param  string    $field
     * @param  string    $value
     * @param  boolean   $is_date
     * @return TaskFilter
     */
    private function filterWithOperator($field, $value, $is_date)
    {
        $operators = array(
            '<=' => 'lte',
            '>=' => 'gte',
            '<' => 'lt',
            '>' => 'gt',
        );

        foreach ($operators as $operator => $method) {
            if (strpos($value, $operator) === 0) {
                $value = substr($value, strlen($operator));
                $this->query->$method($field, $is_date ? $this->dateParser->getTimestampFromIsoFormat($value) : $value);
                return $this;
            }
        }

        if ($is_date) {
            $timestamp = $this->dateParser->getTimestampFromIsoFormat($value);
            $this->query->gte($field, $timestamp);
            $this->query->lte($field, $timestamp + 86399);
        } else {
            $this->query->eq($field, $value);
        }

        return $this;
    }

    /**
     * Use the board_highlight_period for the "recently" keyword
     *
     * @access private
     * @param  string    $field
     * @return TaskFilter
     */
    private function filterRecentlyDate($field)
    {
        $duration = $this->config->get('board_highlight_period', 0);

        if ($duration > 0) {
            $this->query->gte($field, time() - $duration);
        }

        return $this;
    }
}
