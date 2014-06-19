<section id="main">

    <div class="page-header board">
        <h2>
            <?= t('Project "%s"', $current_project_name) ?>
        </h2>
        <ul>
            <?php foreach ($projects as $project_id => $project_name): ?>
            <?php if ($project_id != $current_project_id): ?>
            <li>
                <a href="?controller=board&amp;action=show&amp;project_id=<?= $project_id ?>"><?= Helper\escape($project_name) ?></a>
            </li>
            <?php endif ?>
            <?php endforeach ?>
        </ul>
    </div>

    <section>
        <h3><?= t('Change assignee for the task "%s"', $values['title']) ?></h3>
        <form method="post" action="?controller=board&amp;action=assignTask" autocomplete="off">
            <?= Helper\form_csrf() ?>
            <?= Helper\form_hidden('id', $values) ?>
            <?= Helper\form_hidden('project_id', $values) ?>

            <?= Helper\form_label(t('Assignee'), 'owner_id') ?>
            <?= Helper\form_select('owner_id', $users_list, $values, $errors) ?><br/>

            <div class="form-actions">
                <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
                <?= t('or') ?> <a href="?controller=board&amp;action=show&amp;project_id=<?= $values['project_id'] ?>"><?= t('cancel') ?></a>
            </div>
        </form>
    </section>

</div>