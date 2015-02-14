<div class="tooltip-tasklinks">
    <ul>
    <?php foreach($links as $link): ?>
        <li>
            <strong><?= t($link['label']) ?></strong>
            <?= $this->a(
                $this->e('#'.$link['task_id'].' - '.$link['title']),
                'task', 'show', array('task_id' => $link['task_id'], 'project_id' => $link['project_id']),
                false,
                $link['is_active'] ? '' : 'task-link-closed'
            ) ?>
        </li>
    <?php endforeach ?>
    </ul>
</div>