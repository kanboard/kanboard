<div class="tooltip-large">
    <table class="table-small">
    <?php foreach ($links as $label => $grouped_links): ?>
        <tr>
            <th colspan="4"><?= t($label) ?></th>
        </tr>
        <?php foreach ($grouped_links as $link): ?>
            <tr>
                <td class="column-10">
                    <?= $this->task->getProgress($link).'%' ?>
                </td>
                <td class="column-60">
                    <?= $this->url->link(
                        $this->text->e('#'.$link['task_id'].' '.$link['title']),
                        'TaskViewController', 'show', array('task_id' => $link['task_id']),
                        false,
                        $link['is_active'] ? '' : 'task-link-closed'
                    ) ?>
                </td>
                <td>
                    <?php if (! empty($link['task_assignee_username'])): ?>
                        <?= $this->text->e($link['task_assignee_name'] ?: $link['task_assignee_username']) ?>
                    <?php else: ?>
                        <?= t('Not assigned') ?>
                    <?php endif ?>
                </td>
                <td>
                    <?= $link['project_name'] ?>
                </td>
            </tr>
        <?php endforeach ?>
    <?php endforeach ?>
    </table>
</div>
