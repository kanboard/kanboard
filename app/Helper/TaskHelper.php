<?php

namespace Kanboard\Helper;

use Kanboard\Core\Base;

/**
 * Task helpers
 *
 * @package helper
 * @author  Frederic Guillot
 */
class TaskHelper extends Base
{
    /**
     * Local cache for project columns
     *
     * @access private
     * @var array
     */
    private $columns = array();

    public function getColors()
    {
        return $this->colorModel->getList();
    }

    public function recurrenceTriggers()
    {
        return $this->taskRecurrenceModel->getRecurrenceTriggerList();
    }

    public function recurrenceTimeframes()
    {
        return $this->taskRecurrenceModel->getRecurrenceTimeframeList();
    }

    public function recurrenceBasedates()
    {
        return $this->taskRecurrenceModel->getRecurrenceBasedateList();
    }

    public function selectTitle(array $values, array $errors)
    {
        $html = $this->helper->form->label(t('Title'), 'title');
        $html .= $this->helper->form->text('title', $values, $errors, array('autofocus', 'required', 'maxlength="200"', 'tabindex="1"'), 'form-input-large');
        return $html;
    }

    public function selectDescription(array $values, array $errors)
    {
        $html = $this->helper->form->label(t('Description'), 'description');
        $html .= $this->helper->form->textEditor('description', $values, $errors, array('tabindex' => 2));
        return $html;
    }

    public function selectTags(array $project, array $tags = array())
    {
        $options = $this->tagModel->getAssignableList($project['id']);

        $html = $this->helper->form->label(t('Tags'), 'tags[]');
        $html .= '<input type="hidden" name="tags[]" value="">';
        $html .= '<select name="tags[]" id="form-tags" class="tag-autocomplete" multiple>';

        foreach ($options as $tag) {
            $html .= sprintf(
                '<option value="%s" %s>%s</option>',
                $this->helper->text->e($tag),
                in_array($tag, $tags) ? 'selected="selected"' : '',
                $this->helper->text->e($tag)
            );
        }

        $html .= '</select>';

        return $html;
    }

    public function selectColor(array $values)
    {
        $colors = $this->colorModel->getList();
        $html = $this->helper->form->label(t('Color'), 'color_id');
        $html .= $this->helper->form->select('color_id', $colors, $values, array(), array(), 'color-picker');
        return $html;
    }

    public function selectAssignee(array $users, array $values, array $errors = array(), array $attributes = array())
    {
        $attributes = array_merge(array('tabindex="3"'), $attributes);

        $html = $this->helper->form->label(t('Assignee'), 'owner_id');
        $html .= $this->helper->form->select('owner_id', $users, $values, $errors, $attributes);
        $html .= '&nbsp;';
        $html .= '<small>';
        $html .= '<a href="#" class="assign-me" data-target-id="form-owner_id" data-current-id="'.$this->userSession->getId().'" title="'.t('Assign to me').'">'.t('Me').'</a>';
        $html .= '</small>';

        return $html;
    }

    public function selectCategory(array $categories, array $values, array $errors = array(), array $attributes = array(), $allow_one_item = false)
    {
        $attributes = array_merge(array('tabindex="4"'), $attributes);
        $html = '';

        if (! (! $allow_one_item && count($categories) === 1 && key($categories) == 0)) {
            $html .= $this->helper->form->label(t('Category'), 'category_id');
            $html .= $this->helper->form->select('category_id', $categories, $values, $errors, $attributes);
        }

        return $html;
    }

    public function selectSwimlane(array $swimlanes, array $values, array $errors = array(), array $attributes = array())
    {
        $attributes = array_merge(array('tabindex="5"'), $attributes);
        $html = '';

        if (! (count($swimlanes) === 1 && key($swimlanes) == 0)) {
            $html .= $this->helper->form->label(t('Swimlane'), 'swimlane_id');
            $html .= $this->helper->form->select('swimlane_id', $swimlanes, $values, $errors, $attributes);
        }

        return $html;
    }

    public function selectColumn(array $columns, array $values, array $errors = array(), array $attributes = array())
    {
        $attributes = array_merge(array('tabindex="6"'), $attributes);

        $html = $this->helper->form->label(t('Column'), 'column_id');
        $html .= $this->helper->form->select('column_id', $columns, $values, $errors, $attributes);

        return $html;
    }

    public function selectPriority(array $project, array $values)
    {
        $html = '';

        if ($project['priority_end'] != $project['priority_start']) {
            $range = range($project['priority_start'], $project['priority_end']);
            $options = array_combine($range, $range);
            $values += array('priority' => $project['priority_default']);

            $html .= $this->helper->form->label(t('Priority'), 'priority');
            $html .= $this->helper->form->select('priority', $options, $values, array(), array('tabindex="7"'));
        }

        return $html;
    }

    public function selectScore(array $values, array $errors = array(), array $attributes = array())
    {
        $attributes = array_merge(array('tabindex="8"'), $attributes);

        $html = $this->helper->form->label(t('Complexity'), 'score');
        $html .= $this->helper->form->number('score', $values, $errors, $attributes);

        return $html;
    }

    public function selectReference(array $values, array $errors = array(), array $attributes = array())
    {
        $attributes = array_merge(array('tabindex="9"'), $attributes);

        $html = $this->helper->form->label(t('Reference'), 'reference');
        $html .= $this->helper->form->text('reference', $values, $errors, $attributes, 'form-input-small');

        return $html;
    }

    public function selectTimeEstimated(array $values, array $errors = array(), array $attributes = array())
    {
        $attributes = array_merge(array('tabindex="10"'), $attributes);

        $html = $this->helper->form->label(t('Original estimate'), 'time_estimated');
        $html .= $this->helper->form->numeric('time_estimated', $values, $errors, $attributes);
        $html .= ' '.t('hours');

        return $html;
    }

    public function selectTimeSpent(array $values, array $errors = array(), array $attributes = array())
    {
        $attributes = array_merge(array('tabindex="11"'), $attributes);

        $html = $this->helper->form->label(t('Time spent'), 'time_spent');
        $html .= $this->helper->form->numeric('time_spent', $values, $errors, $attributes);
        $html .= ' '.t('hours');

        return $html;
    }

    public function selectStartDate(array $values, array $errors = array(), array $attributes = array())
    {
        $attributes = array_merge(array('tabindex="12"'), $attributes);
        return $this->helper->form->datetime(t('Start Date'), 'date_started', $values, $errors, $attributes);
    }

    public function selectDueDate(array $values, array $errors = array(), array $attributes = array())
    {
        $attributes = array_merge(array('tabindex="13"'), $attributes);
        return $this->helper->form->date(t('Due Date'), 'date_due', $values, $errors, $attributes);
    }

    public function formatPriority(array $project, array $task)
    {
        $html = '';

        if ($project['priority_end'] != $project['priority_start']) {
            $html .= '<span class="task-board-priority" title="'.t('Task priority').'">';
            $html .= $task['priority'] >= 0 ? 'P'.$task['priority'] : '-P'.abs($task['priority']);
            $html .= '</span>';
        }

        return $html;
    }

    public function getProgress($task)
    {
        if (! isset($this->columns[$task['project_id']])) {
            $this->columns[$task['project_id']] = $this->columnModel->getList($task['project_id']);
        }

        return $this->taskModel->getProgress($task, $this->columns[$task['project_id']]);
    }

    public function getNewTaskDropdown($projectId, $swimlaneId, $columnId)
    {
        $providers = $this->externalTaskManager->getProvidersList();

        if (empty($providers)) {
            return '';
        }

        $html = '<small class="pull-right"><div class="dropdown">';
        $html .= '<a href="#" class="dropdown-menu"><i class="fa fa-cloud-download" aria-hidden="true"></i> <i class="fa fa-caret-down"></i></a><ul>';

        foreach ($providers as $providerName) {
            $link = $this->helper->url->link(
                t('New External Task: %s', $providerName),
                'ExternalTaskCreationController',
                'step1',
                array('project_id' => $projectId, 'swimlane_id' => $swimlaneId, 'column_id' => $columnId, 'provider_name' => $providerName),
                false,
                'popover-link'
            );

            $html .= '<li><i class="fa fa-fw fa-plus-square" aria-hidden="true"></i> '.$link.'</li>';
        }

        $html .= '</ul></div></small>';
        return $html;
    }
}
