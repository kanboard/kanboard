<details class="accordion-section" <?= empty($links) ? '' : 'open' ?>>
    <summary class="accordion-title"><?= t('External links') ?></summary>
    <div class="accordion-content">
        <?= $this->render('task_external_link/table', array(
            'links' => $links,
            'task' => $task,
            'project' => $project,
        )) ?>
    </div>
    <?= $this->modal->medium('external-link', t('Add external link'), 'TaskExternalLinkController', 'find', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
</details>
