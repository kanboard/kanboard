<section id="main">
    <div class="page-header">
        <h2><?= t('Edit a task') ?></h2>
    </div>
    <section>
    <form method="post" action="?controller=task&amp;action=update" autocomplete="off">

        <?= Helper\form_hidden('id', $values) ?>
        <?= Helper\form_hidden('project_id', $values) ?>

        <?= Helper\form_label(t('Title'), 'title') ?>
        <?= Helper\form_text('title', $values, $errors, array('required')) ?><br/>

        <?= Helper\form_label(t('Column'), 'column_id') ?>
        <?= Helper\form_select('column_id', $columns_list, $values, $errors) ?><br/>

        <?= Helper\form_label(t('Color'), 'color_id') ?>
        <?= Helper\form_select('color_id', $colors_list, $values, $errors) ?><br/>

        <?= Helper\form_label(t('Assignee'), 'owner_id') ?>
        <?= Helper\form_select('owner_id', $users_list, $values, $errors) ?><br/>

        <?= Helper\form_label(t('Story Points'), 'score') ?>
        <?= Helper\form_number('score', $values, $errors) ?><br/>

        <?= Helper\form_label(t('Description'), 'description') ?>
        <?= Helper\form_textarea('description', $values, $errors) ?><br/>
        <div class="form-help"><a href="http://en.wikipedia.org/wiki/Markdown#Example" target="_blank"><?= t('Write your text in Markdown') ?></a></div>

        <?= Helper\form_checkbox('another_task', t('Create another task'), 1) ?>

        <div class="form-actions">
            <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
            <?= t('or') ?> <a href="?controller=board&amp;action=show&amp;project_id=<?= $values['project_id'] ?>"><?= t('cancel') ?></a>
        </div>
    </form>
    </section>
</section>