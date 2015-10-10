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
            <td><?= dt('%B %e, %Y at %k:%M %p', $transition['date']) ?></td>
            <td><?= $this->e($transition['src_column']) ?></td>
            <td><?= $this->e($transition['dst_column']) ?></td>
            <td><?= $this->url->link($this->e($transition['name'] ?: $transition['username']), 'user', 'show', array('user_id' => $transition['user_id'])) ?></td>
            <td><?= $this->dt->duration($transition['time_spent']) ?></td>
        </tr>
        <?php endforeach ?>
    </table>
<?php endif ?>