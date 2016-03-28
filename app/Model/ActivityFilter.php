<?php

namespace Kanboard\Model;

/**
 * Activity Filter
 *
 * @package  model
 * @author   Asim Husanovic
 */
class ActivityFilter extends Base
{
    /**
     * Filters mapping
     *
     * @access private
     * @var array
     */
    private $filters = array(
        'T_TITLE' => 'filterByTask',
        'T_CREATOR' => 'filterByCreator',
        'T_CREATED' => 'filterByCreationDate',
        'T_STATUS' => 'filterByStatusName',
        'T_PROJECT' => 'filterByProjectName',
        'T_COMMENT' => 'filterByComment',
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
     * @return ActivityFilter
     */
    public function search($input)
    {
        $tree = $this->lexer->map($this->lexer->tokenize($input));
        $this->query = $this->activityFinder->getExtendedQuery();

        if (empty($tree)) {
            $this->filterByTask($input);
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
     * @return ActivityFilter
     */
    public function create()
    {
        $this->query = $this->db->table(ProjectActivity::TABLE);
        $this->query->left(User::TABLE, 'uc', 'id', ProjectActivity::TABLE, 'creator_id');

        $this->query->columns(
            ProjectActivity::TABLE.'.*',
            'uc.email AS creator_email',
            'uc.username AS creator_username'
        );

        return $this;
    }

    /**
     * Clone the filter
     *
     * @access public
     * @return ActivityFilter
     */
    public function copy()
    {
        $filter = new static($this->container);
        $filter->query = clone($this->query);
        $filter->query->condition = clone($this->query->condition);
        return $filter;
    }

    /**
     * Filter by task title or task id if the string is like #123 or an integer
     *
     * @access public
     * @param  string  $title
     * @return ActivityFilter
     */
    public function filterByTask($title)
    {
        if (ctype_digit($title) || (strlen($title) > 1 && $title{0} === '#' && ctype_digit(substr($title, 1)))) {
            $this->query->beginOr();
            $this->query->eq(ProjectActivity::TABLE.'.task_id', str_replace('#', '', $title));
            $this->query->ilike(Task::TABLE.'.title', '%'.$title.'%');
            $this->query->closeOr();
        } else {
            $this->query->ilike(Task::TABLE.'.title', '%'.$title.'%');
        }

        return $this;
    }

    /**
     * Filter by creator names
     *
     * @access public
     * @param  array    $values   List of creators
     * @return ActivityFilter
     */
    public function filterByCreator(array $values)
    {
        $this->query->beginOr();

        foreach ($values as $creator) {
            switch ($creator) {
                case 'me':
                    $this->query->eq(ProjectActivity::TABLE.'.creator_id', $this->userSession->getId());
                    break;
                case 'nobody':
                    $this->query->eq(ProjectActivity::TABLE.'.creator_id', 0);
                    break;
                default:
                    $this->query->ilike(User::TABLE.'.username', '%'.$creator.'%');
                    $this->query->ilike(User::TABLE.'.name', '%'.$creator.'%');
            }
        }

        $this->query->closeOr();

        return $this;
    }

    /**
     * Filter by status name
     *
     * @access public
     * @param  string  $status
     * @return ActivityFilter
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
     * @return ActivityFilter
     */
    public function filterByStatus($is_active)
    {
        if ($is_active >= 0) {
            $this->query->eq(Task::TABLE.'.is_active', $is_active);
        }

        return $this;
    }

    /**
     * Reorganize data parameters in Event list
     *
     * @access public
     * @param  array    $events   Events
     * @return array
     */
    public function reorganizeDataParameters(array $events)
    {
        foreach ($events as &$event) {
            $event += $this->decode($event['data']);
            unset($event['data']);

            $event['author'] = $event['author_name'] ?: $event['author_username'];
            $event['event_title'] = $this->notification->getTitleWithAuthor($event['author'], $event['event_name'], $event);
            $event['event_content'] = $this->getContent($event);
        }

        return $events;
    }

    /**
     * Filter by a list of project id
     *
     * @access public
     * @param  array  $project_ids
     * @return ActivityFilter
     */
    public function filterByProjects(array $project_ids)
    {
        $this->query
            ->columns(
                ProjectActivity::TABLE.'.*',
                User::TABLE.'.username AS author_username',
                User::TABLE.'.name AS author_name',
                User::TABLE.'.email'
            )
            ->in(ProjectActivity::TABLE.'.project_id', $project_ids)
            ->callback(array($this, 'reorganizeDataParameters'));

        return $this;
    }

    /**
     * Filter by project id
     *
     * @access public
     * @param  integer  $project_id
     * @return ActivityFilter
     */
    public function filterByProject($project_id)
    {
        if ($project_id > 0) {
            $this->query->eq(ProjectActivity::TABLE.'.project_id', $project_id);
        }

        return $this;
    }

    /**
     * Filter by project name
     *
     * @access public
     * @param  array    $values   List of project name
     * @return ActivityFilter
     */
    public function filterByProjectName(array $values)
    {
        $this->query->beginOr();

        foreach ($values as $project) {
            if (ctype_digit($project)) {
                $this->query->eq(ProjectActivity::TABLE.'.project_id', $project);
            } else {
                $this->query->ilike(Project::TABLE.'.name', $project);
            }
        }

        $this->query->closeOr();
    }

    /**
     * Filter by creation date
     *
     * @access public
     * @param  string      $date      ISO8601 date format
     * @return ActivityFilter
     */
    public function filterByCreationDate($date)
    {
        if ($date === 'recently') {
            return $this->filterRecentlyDate(ProjectActivity::TABLE.'.date_creation');
        }

        return $this->filterWithOperator(ProjectActivity::TABLE.'.date_creation', $date, true);
    }

    /**
     * Filter by creation date
     *
     * @access public
     * @param  string  $start
     * @param  string  $end
     * @return ActivityFilter
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
     * Filter by comments
     *
     * @access public
     * @param  string  $string
     * @return ActivityFilter
     */
    public function filterByComment($string) {
        $this->query->beginOr();

        $this->query->ilike(Comment::TABLE.'.comment', '%'.$string.'%');
        $this->query->join(Comment::TABLE, 'task_id', 'task_id');

        $this->query->closeOr();
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
     * Filter with an operator
     *
     * @access public
     * @param  string    $field
     * @param  string    $value
     * @param  boolean   $is_date
     * @return ActivityFilter
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
     * Get the event html content
     *
     * @access public
     * @param  array     $params    Event properties
     * @return string
     */
    public function getContent(array $params)
    {
        return ProjectActivity::getContent($params);
    }

    /**
     * Decode event data, supports unserialize() and json_decode()
     *
     * @access public
     * @param  string   $data   Serialized data
     * @return array
     */
    public function decode($data)
    {
        return ProjectActivity::decode($data);
    }
}
