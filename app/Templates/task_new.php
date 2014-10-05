<section id="main">
    <div class="page-header">
        <h2><?= t('New task') ?></h2>
    </div>
    <section id="task-section">
    <form method="post" action="?controller=task&amp;action=save" autocomplete="off">

        <?= Helper\form_csrf() ?>

        <div class="form-column">
            <?= Helper\form_label(t('Title'), 'title') ?>
            <?= Helper\form_text('title', $values, $errors, array('autofocus', 'required')) ?><br/>

            <?= Helper\form_label(t('Description'), 'description') ?>
            <?= Helper\form_textarea('description', $values, $errors) ?><br/>
            <div class="form-help"><a href="http://kanboard.net/documentation/syntax-guide" target="_blank" rel="noreferrer"><?= t('Write your text in Markdown') ?></a></div>

            <?php if (! isset($duplicate)): ?>
                <?= Helper\form_checkbox('another_task', t('Create another task'), 1, isset($values['another_task']) && $values['another_task'] == 1) ?>
            <?php endif ?>
        </div>

        <div class="form-column">
            <?= Helper\form_hidden('project_id', $values) ?>

            <?= Helper\form_label(t('Assignee'), 'owner_id') ?>
            <?= Helper\form_select('owner_id', $users_list, $values, $errors) ?><br/>

            <?= Helper\form_label(t('Category'), 'category_id') ?>
            <?= Helper\form_select('category_id', $categories_list, $values, $errors) ?><br/>

            <?= Helper\form_label(t('Column'), 'column_id') ?>
            <?= Helper\form_select('column_id', $columns_list, $values, $errors) ?><br/>

            <?= Helper\form_label(t('Color'), 'color_id') ?>
            <?= Helper\form_select('color_id', $colors_list, $values, $errors) ?><br/>

            <?= Helper\form_label(t('Complexity'), 'score') ?>
            <?= Helper\form_number('score', $values, $errors) ?><br/>

            <?= Helper\form_label(t('Due Date'), 'date_due') ?>
            <?= Helper\form_text('date_due', $values, $errors, array('placeholder="'.Helper\in_list($date_format, $date_formats).'"'), 'form-date') ?><br/>
            <div class="form-help"><?= t('Others formats accepted: %s and %s', date('Y-m-d'), date('Y_m_d')) ?></div>
        </div>

        <div class="form-actions">
            <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
            <?= t('or') ?> <a href="?controller=board&amp;action=show&amp;project_id=<?= $values['project_id'] ?>"><?= t('cancel') ?></a>
        </div>
    </form>
    </section>
</section>