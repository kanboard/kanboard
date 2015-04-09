<div class="tooltip-tasklinks">
    <?php foreach ($links as $label => $grouped_links): ?>
        <h1><?= t($label) ?></h1>
        <ul>
        <?php foreach ($grouped_links as $link): ?>
            <li>
                <?= $this->a(
                    $this->e('#'.$link['task_id'].' - '.$link['title']),
                    'task', 'show', array('task_id' => $link['task_id'], 'project_id' => $link['project_id']),
                    false,
                    $link['is_active'] ? '' : 'task-link-closed'
                ) ?>
                <?php if (! empty($link['task_assignee_username'])): ?>
                    [<?= $this->e($link['task_assignee_name'] ?: $link['task_assignee_username']) ?>]
                <?php endif ?>
            </li>
        <?php endforeach ?>
        </ul>
    <?php endforeach ?>
</div>