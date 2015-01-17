<?php

namespace Controller;

/**
 * Project Calendar controller
 *
 * @package  controller
 * @author   Timo Litzbarski
 */
class Calendar extends Base {

    /**
     * Common layout for calendar view
     *
     * @access private
     * @param  string    $template   Template name
     * @param  array     $params     Template parameters
     * @return string
     */
    private function layout($template, array $params) {
        $params['board_selector'] = $this->projectPermission->getAllowedProjects($this->userSession->getId());
        $params['analytic_content_for_layout'] = $this->template->render($template, $params);

        return $this->template->layout('calendar/layout', $params);
    }

    /**
     * Show calendar view
     *
     * @access public
     */
    public function show() {
        $project = $this->getProject();

        $status = array();
        $status[-1] = t('All tasks');
        $status[0] = t('Closed');
        $status[1] = t('Open');

        $columns = array();
        $columns[-1] = t('All columns');
        $columns = array_replace($columns, $this->board->getColumnsList($project['id']));
        
        $swimlanes = array();
        $swimlanes[-1] = t('All swimlanes');
        $swimlanes = array_replace($swimlanes, $this->board->swimlane->getSwimlanesList($project['id']));
        
        $color = array();
        $color[-1] = t('All colors');
        $color = array_replace($color, $this->color->getList());
        
        $projects = array();
        $projects[$project['id']] = $project['name'];

        $this->response->html($this->layout('calendar/show', array(
                    'project' => $project,
                    'users' => $this->projectPermission->getMemberList($project['id'], true, true),
                    'categories' => $this->category->getList($project['id'], true, true),
                    'projects' => $projects,
                    'columns' => $columns,
                    'swimlanes' => $swimlanes,
                    'color' => $color,
                    'status' => $status,
                    'dataurl' => '?controller=calendar&amp;action=events&amp;status_id=1&amp;project_id=' . $project['id'],
                    'interval' => $this->config->get('board_private_refresh_interval'),
                    'title' => t('Task calendar for "%s"', $project['name']),
        )));
    }

    /**
     * Update Event (drag and drop ajax method)
     * 
     * @access public
     */
    public function updateevent() {
        
        $project_id = $this->request->getIntegerParam('project_id');
        
        if ($project_id > 0 && $this->request->isAjax() && $this->request->isPost()) {

            if (! $this->projectPermission->isUserAllowed($project_id, $this->userSession->getId())) {
                $this->response->text('Forbidden', 403);
            }
            
            $values = $this->request->getJson();
            
            $task_id = $values['id'];
            $task_start = $values['start'];

            $task = $this->taskFinder->getById($task_id);
            $date_parts = explode('-', $task_start);
            $start = mktime(0, 0, 0, $date_parts[1], $date_parts[2], $date_parts[0]);
            $task['date_due'] = $start;

            $this->taskModification->update($task);
        }
    }

    /**
     * Get filtered Events (Ajax Call)
     * 
     * @access public
     * @return json
     */
    public function events() {
        $project_id  = $this->request->getIntegerParam('project_id');
        $user_id     = $this->request->getIntegerParam('user_id', -1);
        $category_id = $this->request->getIntegerParam('category_id', -1);
        $column_id   = $this->request->getIntegerParam('column_id', -1);
        $swimlane_id = $this->request->getIntegerParam('swimlane_id', -1);
        $color_id    = $this->request->getStringParam('color_id', '-1');
        $status_id   = $this->request->getIntegerParam('status_id', -1);
        
        $date_start = $this->request->getStringParam('start');
        $date_parts = explode('-', $date_start);
        $date_start = mktime(0, 0, 0, $date_parts[1], $date_parts[2], $date_parts[0]);
        
        $date_end   = $this->request->getStringParam('end');
        $date_parts = explode('-', $date_end);
        $date_end   = mktime(0, 0, 0, $date_parts[1], $date_parts[2], $date_parts[0]);
        
        $tasks = $this->taskFinder->getTaskForCalendar($project_id, $user_id, $category_id, $column_id, $swimlane_id, $status_id, $color_id, $date_start, $date_end);
        
        // List of events
        $event_array = array();
        foreach ($tasks as $task) {
                                    $json_event = array();
                                    $json_event['id'] = $task['id'];
                                    $json_event['title'] = '#' . $task['id'] . ': ' . $task['title'];
                                    $json_event['start'] = date('c', $task['date_due']);          //"start": "2014-09-09T16:00:00-05:00" 
                                    $json_event['end'] = date('c', $task['date_due']);
                                    $json_event['allDay'] = true;                                    
                                    $json_event['backgroundColor'] = $this->color->getBackgroundColor($task['color_id']);
                                    $json_event['borderColor'] = $this->color->getBorderColor($task['color_id']);                                    
                                    $json_event['textColor'] = 'black';
                                    $json_event['url'] = '?controller=task&action=show&task_id=' . $task['id'] . '&project_id=' . $task['project_id'];
                                    array_push($event_array, $json_event);    
        }
        echo json_encode($event_array);
    }

    /**
     * get Texts for calendar (Ajax)
     * 
     * @access public
     * @return json
     */
    public function gettexts() {
        $json = array();
        $json['today'] = t('today');
        $json['Jan'] = t('Jan');
        $json['Feb'] = t('Feb');
        $json['Mar'] = t('Mar');
        $json['Apr'] = t('Apr');
        $json['May'] = t('May');
        $json['Jun'] = t('Jun');
        $json['Jul'] = t('Jul');
        $json['Aug'] = t('Aug');
        $json['Sep'] = t('Sep');
        $json['Oct'] = t('Oct');
        $json['Nov'] = t('Nov');
        $json['Dec'] = t('Dec');
        $json['January'] = t('January');
        $json['February'] = t('February');
        $json['March'] = t('March');
        $json['April'] = t('April');
        $json['May'] = t('May');
        $json['June'] = t('June');
        $json['July'] = t('July');
        $json['August'] = t('August');
        $json['September'] = t('September');
        $json['October'] = t('October');
        $json['November'] = t('November');
        $json['December'] = t('December');
        $json['Sunday'] = t('Sunday');
        $json['Monday'] = t('Monday');
        $json['Tuesday'] = t('Tuesday');
        $json['Wednesday'] = t('Wednesday');
        $json['Thursday'] = t('Thursday');
        $json['Friday'] = t('Friday');
        $json['Saturday'] = t('Saturday');
        $json['Sun'] = t('Sun');
        $json['Mon'] = t('Mon');
        $json['Tue'] = t('Tue');
        $json['Wed'] = t('Wed');
        $json['Thu'] = t('Thu');
        $json['Fri'] = t('Fri');
        $json['Sat'] = t('Sat');

        echo json_encode($json);
    }

}
