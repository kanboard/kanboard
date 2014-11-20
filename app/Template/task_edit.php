<div class="page-header">
    <h2><?= t('Edit a task') ?></h2>
</div>
<section id="task-section">
<form method="post" action="?controller=task&amp;action=update&amp;task_id=<?= $task['id'] ?>&amp;ajax=<?= $ajax ?>" autocomplete="off">

    <?= Helper\form_csrf() ?>

    <div class="form-column">

        <?= Helper\form_label(t('Title'), 'title') ?>
        <?= Helper\form_text('title', $values, $errors, array('required')) ?><br/>

        <?= Helper\form_label(t('Description'), 'description') ?>
        <div class="form-tabs">
            <div class="form-tabs-nav">
                <a id="w" class="form-tab form-tab-selected btn btn-small" href="#w"><?= t('Write') ?></a>
                <a id="p" class="form-tab btn btn-small" href="#p"v><?= t('Preview') ?></a>
                <span class="form-help pull-right"><a href="http://kanboard.net/documentation/syntax-guide" target="_blank" rel="noreferrer"><i class="octicon octicon-markdown"></i> <?= t('Markdown supported') ?></a></span>
            </div>
            <div class="write-area form-tab">
                <?= Helper\form_textarea('description', $values, $errors) ?>
            </div>
            <div class="preview-area form-tab">
                <div class="markdown"></div>
            </div>
        </div>    

    </div>

    <div class="form-column">
        <?= Helper\form_hidden('id', $values) ?>
        <?= Helper\form_hidden('project_id', $values) ?>

        <?= Helper\form_label(t('Assignee'), 'owner_id') ?>
        <?= Helper\form_select('owner_id', $users_list, $values, $errors) ?><br/>

        <?= Helper\form_label(t('Category'), 'category_id') ?>
        <?= Helper\form_select('category_id', $categories_list, $values, $errors) ?><br/>

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
        <?= t('or') ?>
        <?php if ($ajax): ?>
            <a href="?controller=board&amp;action=show&amp;project_id=<?= $task['project_id'] ?>"><?= t('cancel') ?></a>
        <?php else: ?>
            <a href="?controller=task&amp;action=show&amp;task_id=<?= $task['id'] ?>"><?= t('cancel') ?></a>
        <?php endif ?>
    </div>
</form>
</section>
