<section id="main">
    <div class="page-header">
        <ul>
            <?php if (Helper\is_admin()): ?>
                <li><i class="fa fa-plus fa-fw"></i><?= Helper\a(t('New project'), 'project', 'create') ?></li>
            <?php endif ?>
            <li><i class="fa fa-lock fa-fw"></i><?= Helper\a(t('New private project'), 'project', 'create', array('private' => 1)) ?></li>
            <li><i class="fa fa-folder fa-fw"></i><?= Helper\a(t('Project management'), 'project', 'index') ?></li>
            <?php if (Helper\is_admin()): ?>
                <li><i class="fa fa-user fa-fw"></i><?= Helper\a(t('User management'), 'user', 'index') ?></li>
                <li><i class="fa fa-cog fa-fw"></i><?= Helper\a(t('Settings'), 'config', 'index') ?></li>
            <?php endif ?>
        </ul>
    </div>
    <section id="dashboard">
        <div class="dashboard-left-column">
            <h2><?= t('My tasks') ?></h2>
            <?php if (empty($tasks)): ?>
                <p class="alert"><?= t('There is nothing assigned to you.') ?></p>
            <?php else: ?>
                <table>
                    <tr>
                        <th>&nbsp;</th>
                        <th width="15%"><?= t('Project') ?></th>
                        <th width="40%"><?= t('Title') ?></th>
                        <th><?= t('Due date') ?></th>
                        <th><?= t('Date created') ?></th>
                    </tr>
                    <?php foreach ($tasks as $task): ?>
                    <tr>
                        <td class="task-table task-<?= $task['color_id'] ?>">
                            <?= Helper\a('#'.$task['id'], 'task', 'show', array('task_id' => $task['id'])) ?>
                        </td>
                        <td>
                            <?= Helper\a(Helper\escape($task['project_name']), 'board', 'show', array('project_id' => $task['project_id'])) ?>
                        </td>
                        <td>
                            <?= Helper\a(Helper\escape($task['title']), 'task', 'show', array('task_id' => $task['id'])) ?>
                        </td>
                        <td>
                            <?= dt('%B %e, %Y', $task['date_due']) ?>
                        </td>
                        <td>
                            <?= dt('%B %e, %Y', $task['date_creation']) ?>
                        </td>
                    </tr>
                    <?php endforeach ?>
                </table>
            <?php endif ?>
        </div>
        <div class="dashboard-right-column">
            <h2><?= t('Activity stream') ?></h2>
            <?= Helper\template('project_events', array('events' => $events)) ?>
    </section>
</section>