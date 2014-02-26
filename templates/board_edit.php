<section id="main">
    <div class="page-header">
        <h2><?= t('Edit the board for "%s"', $project['name']) ?></h2>
        <ul>
            <li><a href="?controller=project"><?= t('All projects') ?></a></li>
        </ul>
    </div>
    <section>

    <h3><?= t('Change columns') ?></h3>
    <form method="post" action="?controller=board&amp;action=update&amp;project_id=<?= $project['id'] ?>" autocomplete="off">

        <?php $i = 0; ?>

        <?php foreach ($columns as $column): ?>
            <?= Helper\form_label(t('Column %d', ++$i), 'title['.$column['id'].']') ?>
            <?= Helper\form_text('title['.$column['id'].']', $values, $errors, array('required')) ?>
            <?= Helper\form_number('task_limit['.$column['id'].']', $values, $errors, array('placeholder="'.t('limit').'"')) ?>
            <a href="?controller=board&amp;action=confirm&amp;project_id=<?= $project['id'] ?>&amp;column_id=<?= $column_id ?>"><?= t('Remove') ?></a>
        <?php endforeach ?>

        <div class="form-actions">
            <input type="submit" value="<?= t('Update') ?>" class="btn btn-blue"/>
            <?= t('or') ?> <a href="?controller=project"><?= t('cancel') ?></a>
        </div>
    </form>

    <h3><?= t('Add a new column') ?></h3>
    <form method="post" action="?controller=board&amp;action=add&amp;project_id=<?= $project['id'] ?>" autocomplete="off">

        <?= Helper\form_hidden('project_id', $values) ?>
        <?= Helper\form_label(t('Title'), 'title') ?>
        <?= Helper\form_text('title', $values, $errors, array('required')) ?>

        <div class="form-actions">
            <input type="submit" value="<?= t('Add this column') ?>" class="btn btn-blue"/>
            <?= t('or') ?> <a href="?controller=project"><?= t('cancel') ?></a>
        </div>
    </form>
    </section>
</section>