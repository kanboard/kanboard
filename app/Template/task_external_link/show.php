<details class="accordion-section" <?= empty($links) ? '' : 'open' ?>>
    <summary class="accordion-title"><?= t('External links') ?></summary>
    <div class="accordion-content">
        <?= $this->render('task_external_link/table', array(
            'links' => $links,
            'task' => $task,
            'project' => $project,
        )) ?>
    </div>
</details>
