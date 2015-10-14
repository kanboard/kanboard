<?php

namespace Kanboard\Controller;

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
        $params = $this->getProjectFilters('listing', 'show');
        $query = $this->taskFilter->search($params['filters']['search'])->filterByProject($params['project']['id'])->getQuery();

        $paginator = $this->paginator
            ->setUrl('listing', 'show', array('project_id' => $params['project']['id']))
            ->setMax(30)
            ->setOrder(TaskModel::TABLE.'.id')
            ->setDirection('DESC')
            ->setQuery($query)
            ->calculate();

        $this->response->html($this->template->layout('listing/show', $params + array(
            'paginator' => $paginator,
        )));
    }
}
