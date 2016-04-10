<?php

namespace Kanboard\Controller;

use Kanboard\Filter\TaskProjectFilter;
use Kanboard\Model\Task as TaskModel;

/**
 * List view controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Listing extends Base
{
    /**
     * Show list view for projects
     *
     * @access public
     */
    public function show()
    {
        $project = $this->getProject();
        $search = $this->helper->projectHeader->getSearchQuery($project);

        $paginator = $this->paginator
            ->setUrl('listing', 'show', array('project_id' => $project['id']))
            ->setMax(30)
            ->setOrder(TaskModel::TABLE.'.id')
            ->setDirection('DESC')
            ->setQuery($this->taskLexer
                ->build($search)
                ->withFilter(new TaskProjectFilter($project['id']))
                ->getQuery()
            )
            ->calculate();

        $this->response->html($this->helper->layout->app('listing/show', array(
            'project' => $project,
            'title' => $project['name'],
            'description' => $this->helper->projectHeader->getDescription($project),
            'paginator' => $paginator,
        )));
    }
}
