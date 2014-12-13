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
        $params['board_selector'] = $this->projectPermission->getAllowedProjects($this->acl->getUserId());
        $params['analytic_content_for_layout'] = $this->template->load($template, $params);

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

        $projects = array();
        $projects[$project['id']] = $project['name'];

        $this->response->html($this->layout('calendar/show', array(
                    'project' => $project,
                    'users' => $this->projectPermission->getMemberList($project['id'], true, true),
                    'categories' => $this->category->getList($project['id'], true, true),
                    'projects' => $projects,
                    'columns' => $columns,
                    'status' => $status,
                    'dataurl' => '?controller=calendar&amp;action=events&amp;status_id=1&amp;project_id=' . $project['id'],
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

            if (! $this->projectPermission->isUserAllowed($project_id, $this->acl->getUserId())) {
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

        $project_id = $this->request->getIntegerParam('project_id');
        $user_id = $this->request->getIntegerParam('user_id', -1);
        $category_id = $this->request->getIntegerParam('category_id', -1);
        $column_id = $this->request->getIntegerParam('column_id', -1);
        $status_id = $this->request->getIntegerParam('status_id', -1);

        if ($status_id == -1) {            
            $tasks1 = $this->taskFinder->getAll($project_id, 1);
            $tasks2 = $this->taskFinder->getAll($project_id, 0);
            $tasks = array_merge($tasks1, $tasks2);            
        } else {
            $tasks = $this->taskFinder->getAll($project_id, $status_id);
        }

        // List of events
        $event_array = array();

        foreach ($tasks as $task) {
            if ($task['date_due'] > 0) {
                if ($project_id == -1 || $task['project_id'] == $project_id) {
                    if ($status_id == -1 || $task['is_active'] == $status_id) {
                        if ($user_id == -1 || $task['owner_id'] == $user_id) {
                            if ($category_id == -1 || $task['category_id'] == $category_id) {
                                if ($column_id == -1 || $task['column_id'] == $column_id) {
                                    $json_event = array();
                                    $json_event['id'] = $task['id'];
                                    $json_event['title'] = '#' . $task['id'] . ': ' . $task['title'];
                                    $json_event['start'] = date('c', $task['date_due']);          //"start": "2014-09-09T16:00:00-05:00" 
                                    $json_event['end'] = date('c', $task['date_due']);
                                    $json_event['allDay'] = true;

                                    /* task colors */
                                    // TODO: model for the future color picker
                                    if ($task['color_id'] === 'blue') {
                                        $json_event['backgroundColor'] = 'rgb(219, 235, 255)';
                                        $json_event['borderColor'] = 'rgb(168, 207, 255)';
                                    } elseif ($task['color_id'] === 'purple') {
                                        $json_event['backgroundColor'] = 'rgb(223, 176, 255)';
                                        $json_event['borderColor'] = 'rgb(205, 133, 254)';
                                    } elseif ($task['color_id'] === 'grey') {
                                        $json_event['backgroundColor'] = 'rgb(238, 238, 238)';
                                        $json_event['borderColor'] = 'rgb(204, 204, 204)';
                                    } elseif ($task['color_id'] === 'red') {
                                        $json_event['backgroundColor'] = 'rgb(255, 187, 187)';
                                        $json_event['borderColor'] = 'rgb(255, 151, 151)';
                                    } elseif ($task['color_id'] === 'green') {
                                        $json_event['backgroundColor'] = 'rgb(189, 244, 203)';
                                        $json_event['borderColor'] = 'rgb(74, 227, 113)';
                                    } elseif ($task['color_id'] === 'yellow') {
                                        $json_event['backgroundColor'] = 'rgb(245, 247, 196)';
                                        $json_event['borderColor'] = 'rgb(223, 227, 45)';
                                    } elseif ($task['color_id'] === 'orange') {
                                        $json_event['backgroundColor'] = 'rgb(255, 215, 179)';
                                        $json_event['borderColor'] = 'rgb(255, 172, 98)';
                                    }

                                    $json_event['textColor'] = 'black';
                                    $json_event['url'] = '?controller=task&action=show&task_id=' . $task['id'];

                                    //additional fields:
                                    /* $json_event['project_id'] = $task['project_id'];
                                      $json_event['column_id'] = $task['column_id'];
                                      $json_event['owner_id'] = $task['owner_id'];
                                      $json_event['is_active'] = $task['is_active'];
                                      $json_event['category_id'] = $task['category_id']; */

                                    array_push($event_array, $json_event);
                                }
                            }
                        }
                    }
                }
            }
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
