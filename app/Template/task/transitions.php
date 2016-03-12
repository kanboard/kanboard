<div class="task-show-title color-<?= $task['color_id'] ?>">
    <h2><?= $this->text->e($task['title']) ?></h2>
</div>

<div class="page-header">
    <h2><?= t('Transitions') ?></h2>
</div>

<?php if (empty($transitions)): ?>
    <p class="alert"><?= t('There is nothing to show.') ?></p>
<?php else: ?>
    <table class="table-stripped">
        <tr>
            <th><?= t('Date') ?></th>
            <th><?= t('Source column') ?></th>
            <th><?= t('Destination column') ?></th>
            <th><?= t('Executer') ?></th>
            <th><?= t('Time spent in the column') ?></th>
        </tr>
        <?php foreach ($transitions as $transition): ?>
        <tr>
            <td><?= $this->dt->datetime($transition['date']) ?></td>
            <td><?= $this->text->e($transition['src_column']) ?></td>
            <td><?= $this->text->e($transition['dst_column']) ?></td>
            <td><?= $this->url->link($this->text->e($transition['name'] ?: $transition['username']), 'user', 'show', array('user_id' => $transition['user_id'])) ?></td>
            <td><?= $this->dt->duration($transition['time_spent']) ?></td>
        </tr>
        <?php endforeach ?>
    </table>
<?php endif ?>