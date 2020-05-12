<details class="accordion-section" <?= empty($files) && empty($images) ? '' : 'open' ?>>
    <summary class="accordion-title"><?= t('Attachments') ?></summary>
    <div class="accordion-content">
        <?= $this->render('task_file/images', array('task' => $task, 'images' => $images)) ?>
        <?= $this->render('task_file/files', array('task' => $task, 'files' => $files)) ?>
        <?= $this->modal->medium('file', t('Attach a document'), 'TaskFileController', 'create', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        <?= $this->modal->medium('camera', t('Add a screenshot'), 'TaskFileController', 'screenshot', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
    </div>
</details>
