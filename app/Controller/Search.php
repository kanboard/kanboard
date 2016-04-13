<?php

namespace Kanboard\Controller;

use Kanboard\Filter\TaskProjectsFilter;

/**
 * Search controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Search extends Base
{
    public function index()
    {
        $projects = $this->projectUserRole->getProjectsByUser($this->userSession->getId());
        $search = urldecode($this->request->getStringParam('search'));
        $nb_tasks = 0;

        $paginator = $this->paginator
                ->setUrl('search', 'index', array('search' => $search))
                ->setMax(30)
                ->setOrder('tasks.id')
                ->setDirection('DESC');

        if ($search !== '' && ! empty($projects)) {
            $paginator
                ->setQuery($this->taskLexer
                    ->build($search)
                    ->withFilter(new TaskProjectsFilter(array_keys($projects)))
                    ->getQuery()
                )
                ->calculate();

            $nb_tasks = $paginator->getTotal();
        }

        $this->response->html($this->helper->layout->app('search/index', array(
            'values' => array(
                'search' => $search,
                'controller' => 'search',
                'action' => 'index',
            ),
            'paginator' => $paginator,
            'title' => t('Search tasks').($nb_tasks > 0 ? ' ('.$nb_tasks.')' : '')
        )));
    }

    public function activity()
    {
        $search = urldecode($this->request->getStringParam('search'));
        $events = $this->helper->projectActivity->searchEvents($search);
        $nb_events = count($events);

        $this->response->html($this->helper->layout->app('search/activity', array(
            'values' => array(
                'search' => $search,
                'controller' => 'search',
                'action' => 'activity',
            ),
            'title' => t('Search in activity stream').($nb_events > 0 ? ' ('.$nb_events.')' : ''),
            'nb_events' => $nb_events,
            'events' => $events,
        )));
    }
}
