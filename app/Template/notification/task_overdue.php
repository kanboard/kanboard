<h2><?= t('Overdue tasks for the project(s) "%s"', $project_name) ?></h2>

<table style="font-size: .8em; table-layout: fixed; width: 100%; border-collapse: collapse; border-spacing: 0; margin-bottom: 20px;" cellpadding=5 cellspacing=1>
    <tr style="background: #fbfbfb; text-align: left; padding-top: .5em; padding-bottom: .5em; padding-left: 3px; padding-right: 3px;">
        <th style="border: 1px solid #eee;"><?= t('ID') ?></th>
        <th style="border: 1px solid #eee;"><?= t('Title') ?></th>
        <th style="border: 1px solid #eee;"><?= t('Due date') ?></th>
        <th style="border: 1px solid #eee;"><?= t('Project') ?></th>
        <th style="border: 1px solid #eee;"><?= t('Assignee') ?></th>
    </tr>

    <?php foreach ($tasks as $task): ?>
        <tr style="overflow: hidden; background: #fff; text-align: left; padding-top: .5em; padding-bottom: .5em; padding-left: 3px; padding-right: 3px;">
            <td style="border: 1px solid #eee;">#<?= $task['id'] ?></td>
            <td style="border: 1px solid #eee;">
                <?php if ($application_url): ?>
                    <a href="<?= $this->url->href('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, '', true) ?>"><?= $this->text->e($task['title']) ?></a>
                <?php else: ?>
                    <?= $this->text->e($task['title']) ?>
                <?php endif ?>
            </td>
            <td style="border: 1px solid #eee;"><?= $this->dt->date($task['date_due']) ?></td>
            <td style="border: 1px solid #eee;"><?= $task['project_name'] ?></td>
            <td style="border: 1px solid #eee;">
                <?php if ($task['assignee_username']): ?>
                    <?= t('%s', $task['assignee_name'] ?: $task['assignee_username']) ?>
                <?php endif ?>
            </td>
        </tr>
    <?php endforeach ?>
</table>
