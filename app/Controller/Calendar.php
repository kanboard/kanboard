<?php

namespace Controller;

/**
 * Project Anaytic controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Calendar extends Base
{
    /**
     * Common layout for calendar view
     *
     * @access private
     * @param  string    $template   Template name
     * @param  array     $params     Template parameters
     * @return string
     */
    private function layout($template, array $params)
    {
        $params['board_selector'] = $this->projectPermission->getAllowedProjects($this->acl->getUserId());
        $params['analytic_content_for_layout'] = $this->template->load($template, $params);

        return $this->template->layout('calendar/layout', $params);
    }

    /**
     * Show calendar view
     *
     * @access public
     */
    public function show()
    {
        $project = $this->getProject();
        
        $this->response->html($this->layout('calendar/show', array(
            'project' => $project,
            'users' => $this->projectPermission->getMemberList($project['id'], true, true),
            'categories' => $this->category->getList($project['id'], true, true),
            'dataurl' => '?controller=calendar&amp;action=events&amp;project_id='.$project['id'],
            'title' => t('Task calendar for "%s"', $project['name']),
        )));
        
    }
    
    public function ical()
    {
        //TODO
    }
    
    public function events()
    {
        
        /*$project_id = $this->request->getIntegerParam('project_id', 0);
        $user_id = $this->request->getIntegerParam('user_id', 0);
        $category_id = $this->request->getIntegerParam('category_id', 0);*/
        
        $project = $this->getProject();
        $project_id = $project['id'];
        
        $tasks = $this->taskFinder->getAll($project_id);
        
        /* $tasks = $this->taskFinder->getQuery()
                    ->eq('project_id', $project_id)
                    ->eq('owner_id', $user_id)
                    ->eq('category_id', $category_id)
                    //->eq('is_active', Task::STATUS_OPEN)
                    ->asc('tasks.position')
                    ->findAll();*/
        
        // List of events
        $array = array();
        
        foreach ($tasks as $task) {
            if ($task['date_due'] > 0) {
                $json = array();
                $json['id'] = $task['id'];
                $json['title'] = '#'.$task['id'].': '. $task['title'];
                $json['start'] = date('c', $task['date_due']);          //"start": "2014-09-09T16:00:00-05:00" 
                $json['allDay'] = true;
                
                /* task colors */
                // TODO: model for the future color picker
                if($task['color_id'] === 'blue') {
                    $json['backgroundColor'] = 'rgb(219, 235, 255)';
                    $json['borderColor'] = 'rgb(168, 207, 255)';
                } elseif($task['color_id'] === 'purple') {
                    $json['backgroundColor'] = 'rgb(223, 176, 255)';
                    $json['borderColor'] = 'rgb(205, 133, 254)';
                } elseif($task['color_id'] === 'grey') {
                    $json['backgroundColor'] = 'rgb(238, 238, 238)';
                    $json['borderColor'] = 'rgb(204, 204, 204)';
                } elseif($task['color_id'] === 'red') {
                    $json['backgroundColor'] = 'rgb(255, 187, 187)';
                    $json['borderColor'] = 'rgb(255, 151, 151)';
                } elseif($task['color_id'] === 'green') {
                    $json['backgroundColor'] = 'rgb(189, 244, 203)';
                    $json['borderColor'] = 'rgb(74, 227, 113)';
                } elseif($task['color_id'] === 'yellow') {
                    $json['backgroundColor'] = 'rgb(245, 247, 196)';
                    $json['borderColor'] = 'rgb(223, 227, 45)';
                } elseif($task['color_id'] === 'orange') {
                    $json['backgroundColor'] = 'rgb(255, 215, 179)';
                    $json['borderColor'] = 'rgb(255, 172, 98)';
                }
                
                $json['textColor'] = 'black';
                $json['url'] = '?controller=task&action=show&task_id=' . $task['id'];

                //additional fields:
                $json['project_id'] = $task['project_id'];
                $json['column_id'] = $task['column_id'];
                $json['owner_id'] = $task['owner_id'];
                $json['is_active'] = $task['is_active'];
                $json['category_id'] = $task['category_id'];
        
    
                array_push($array, $json);
            }
        }
       echo json_encode($array);
    }
}
