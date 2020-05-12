<details class="accordion-section" <?= empty($subtasks) ? '' : 'open' ?>>
    <summary class="accordion-title"><?= t('Sub-Tasks') ?></summary>
    <div class="accordion-content">
        <?= $this->render('subtask/table', array(
            'subtasks' => $subtasks,
            'task' => $task,
            'editable' => $editable
        )) ?>
        <?= $this->modal->medium('plus', t('Add a sub-task'), 'SubtaskController', 'create', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
    </div>
</details>
