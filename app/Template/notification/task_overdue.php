<h2><?= t('Overdue tasks for the project(s) "%s"', $project_name) ?></h2>

<table style="border: 1px solid silver;" cellpadding=5 cellspacing=1>
    <tr style="background-color:silver">
        <th>ID</th>
        <th>Title</th>
        <th>Due date</th>
        <th>Project name</th>
        <th>Assignee</th>
    </tr>

    <?php 
    $ii=0;
    foreach ($tasks as $task):
        $color = ($ii%2 == 0) ? "#F4F9FC" : "white";
        $ii++;
    ?>
        <tr style="background-color:<?= $color ?>">
            <td>#<?= $task['id'] ?></td>
            <td>
                <?php if ($application_url): ?>
                    <a href="<?= $this->url->href('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, '', true) ?>"><?= $this->e($task['title']) ?></a>
                <?php else: ?>
                    <?= $this->e($task['title']) ?>
                <?php endif ?>
            </td>
            <td><?= dt('%B %e, %Y', $task['date_due']) ?></td>
            <td><?= $task['project_name'] ?></td>
            <td>
                <?php if ($task['assignee_username']): ?>
                    <?= t('%s', $task['assignee_name'] ?: $task['assignee_username']) ?>
                <?php endif ?>
            </td>
        </tr>
    <?php endforeach ?>
</table>