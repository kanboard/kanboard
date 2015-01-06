<?php if (! $ajax): ?>
<div class="page-header">
    <ul>
        <li><i class="fa fa-table fa-fw"></i><?= $this->a(t('Back to the board'), 'board', 'show', array('project_id' => $values['project_id'])) ?></li>
    </ul>
</div>
<?php else: ?>
<div class="page-header">
    <h2><?= t('New task') ?></h2>
</div>
<?php endif ?>

<section id="task-section">
<form method="post" action="<?= $this->u('task', 'save', array('project_id' => $values['project_id'])) ?>" autocomplete="off">

    <?= $this->formCsrf() ?>

    <div class="form-column">
        <?= $this->formLabel(t('Title'), 'title') ?>
        <?= $this->formText('title', $values, $errors, array('autofocus', 'required'), 'form-input-large') ?><br/>

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

        <?php if (! isset($duplicate)): ?>
            <?= $this->formCheckbox('another_task', t('Create another task'), 1, isset($values['another_task']) && $values['another_task'] == 1) ?>
        <?php endif ?>
    </div>

    <div class="form-column">
        <?= $this->formHidden('project_id', $values) ?>
        <?= $this->formHidden('swimlane_id', $values) ?>

        <?= $this->formLabel(t('Assignee'), 'owner_id') ?>
        <?= $this->formSelect('owner_id', $users_list, $values, $errors) ?><br/>

        <?= $this->formLabel(t('Category'), 'category_id') ?>
        <?= $this->formSelect('category_id', $categories_list, $values, $errors) ?><br/>

        <?= $this->formLabel(t('Column'), 'column_id') ?>
        <?= $this->formSelect('column_id', $columns_list, $values, $errors) ?><br/>

        <?= $this->formLabel(t('Color'), 'color_id') ?>
        <?= $this->formSelect('color_id', $colors_list, $values, $errors) ?><br/>

        <?= $this->formLabel(t('Complexity'), 'score') ?>
        <?= $this->formNumber('score', $values, $errors) ?><br/>

        <?= $this->formLabel(t('Original estimate'), 'time_estimated') ?>
        <?= $this->formNumeric('time_estimated', $values, $errors) ?> <?= t('hours') ?><br/>

        <?= $this->formLabel(t('Due Date'), 'date_due') ?>
        <?= $this->formText('date_due', $values, $errors, array('placeholder="'.$this->inList($date_format, $date_formats).'"'), 'form-date') ?><br/>
        <div class="form-help"><?= t('Others formats accepted: %s and %s', date('Y-m-d'), date('Y_m_d')) ?></div>
    </div>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
        <?= t('or') ?> <?= $this->a(t('cancel'), 'board', 'show', array('project_id' => $values['project_id'])) ?>
    </div>
</form>
</section>
