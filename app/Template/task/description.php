<details class="accordion-section" <?= empty($task['description']) ? '' : 'open' ?>>
    <summary class="accordion-title"><?= t('Description') ?></summary>
    <div class="accordion-content">
        <article class="markdown">
            <?= $this->text->markdown($task['description'], isset($is_public) && $is_public) ?>
        </article>
        <?= $this->modal->large('edit', t('Edit the task'), 'TaskModificationController', 'edit', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
    </div>
</details>
