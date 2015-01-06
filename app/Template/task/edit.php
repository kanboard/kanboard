<div class="page-header">
    <h2><?= t('Edit a task') ?></h2>
</div>
<section id="task-section">
<form method="post" action="<?= $this->u('task', 'update', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'ajax' => $ajax)) ?>" autocomplete="off">

    <?= $this->formCsrf() ?>

    <div class="form-column">

        <?= $this->formLabel(t('Title'), 'title') ?>
        <?= $this->formText('title', $values, $errors, array('required')) ?><br/>

        <?= $this->formLabel(t('Description'), 'description') ?>

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
                <?= $this->formTextarea('description', $values, $errors, array('placeholder="'.t('Leave a description').'"')) ?>
            </div>
            <div class="preview-area">
                <div class="markdown"></div>
            </div>
        </div>

        <div class="form-help"><a href="http://kanboard.net/documentation/syntax-guide" target="_blank" rel="noreferrer"><?= t('Write your text in Markdown') ?></a></div>

    </div>

    <div class="form-column">
        <?= $this->formHidden('id', $values) ?>
        <?= $this->formHidden('project_id', $values) ?>

        <?= $this->formLabel(t('Assignee'), 'owner_id') ?>
        <?= $this->formSelect('owner_id', $users_list, $values, $errors) ?><br/>

        <?= $this->formLabel(t('Category'), 'category_id') ?>
        <?= $this->formSelect('category_id', $categories_list, $values, $errors) ?><br/>

        <?= $this->formLabel(t('Color'), 'color_id') ?>
        <?= $this->formSelect('color_id', $colors_list, $values, $errors) ?><br/>

        <?= $this->formLabel(t('Complexity'), 'score') ?>
        <?= $this->formNumber('score', $values, $errors) ?><br/>

        <?= $this->formLabel(t('Due Date'), 'date_due') ?>
        <?= $this->formText('date_due', $values, $errors, array('placeholder="'.$this->inList($date_format, $date_formats).'"'), 'form-date') ?><br/>
        <div class="form-help"><?= t('Others formats accepted: %s and %s', date('Y-m-d'), date('Y_m_d')) ?></div>
    </div>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
        <?= t('or') ?>
        <?php if ($ajax): ?>
            <?= $this->a(t('cancel'), 'board', 'show', array('project_id' => $task['project_id'])) ?>
        <?php else: ?>
            <?= $this->a(t('cancel'), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        <?php endif ?>
    </div>
</form>
</section>
