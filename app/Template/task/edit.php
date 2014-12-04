<div class="page-header">
    <h2><?= t('Edit a task') ?></h2>
</div>
<section id="task-section">
<form method="post" action="<?= Helper\u('task', 'update', array('task_id' => $task['id'], 'ajax' => $ajax)) ?>" autocomplete="off">

    <?= Helper\form_csrf() ?>

    <div class="form-column">

        <?= Helper\form_label(t('Title'), 'title') ?>
        <?= Helper\form_text('title', $values, $errors, array('required')) ?><br/>

        <?= Helper\form_label(t('Description'), 'description') ?>

        <div class="form-tabs">
            <ul class="form-tabs-nav">
                <li class="form-tab form-tab-selected">
                    <i class="fa fa-pencil-square-o fa-fw"></i><a id="markdown-write" href="#"><?= t('Write') ?></a>
                </li>
                <li class="form-tab">
                    <a id="markdown-preview" href="#"><i class="fa fa-eye fa-fw"></i><?= t('Preview') ?></a>
                </li>
            </ul>
            <div class="write-area">
                <?= Helper\form_textarea('description', $values, $errors, array('placeholder="'.t('Leave a description').'"')) ?>
            </div>
            <div class="preview-area">
                <div class="markdown"></div>
            </div>
        </div>

        <div class="form-help"><a href="http://kanboard.net/documentation/syntax-guide" target="_blank" rel="noreferrer"><?= t('Write your text in Markdown') ?></a></div>

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
            <?= Helper\a(t('cancel'), 'board', 'show', array('project_id' => $task['project_id'])) ?>
        <?php else: ?>
            <?= Helper\a(t('cancel'), 'task', 'show', array('task_id' => $task['id'])) ?>
        <?php endif ?>
    </div>
</form>
</section>
