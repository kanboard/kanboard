<div class="tooltip-tasklinks">
    <ul>
    <?php foreach ($links as $link): ?>
        <li>
            <strong><?= t($link['label']) ?></strong>
            [<i><?= $link['project_name'] ?></i>]
            <?= $this->url->link(
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
</div>