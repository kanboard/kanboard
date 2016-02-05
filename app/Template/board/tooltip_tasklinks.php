<div class="tooltip-tasklinks">
    <dl>
    <?php foreach ($links as $label => $grouped_links): ?>
        <dt><strong><?= t($label) ?></strong></dt>
        <?php foreach ($grouped_links as $link): ?>
            <dd>
                <span class="progress"><?= $this->task->getProgress($link).'%' ?></span>
                <?= $this->url->link(
                    $this->e('#'.$link['task_id'].' '.$link['title']),
                    'task', 'show', array('task_id' => $link['task_id'], 'project_id' => $link['project_id']),
                    false,
                    $link['is_active'] ? '' : 'task-link-closed'
                ) ?>
                <?php if (! empty($link['task_assignee_username'])): ?>
                    [<?= $this->e($link['task_assignee_name'] ?: $link['task_assignee_username']) ?>]
                <?php endif ?>
                <?php if ($task['project_id'] != $link['project_id']): ?>
                    (<i><?= $link['project_name'] ?></i>)
                <?php endif ?>
            </dd>
        <?php endforeach ?>
    <?php endforeach ?>
    </dl>
</div>