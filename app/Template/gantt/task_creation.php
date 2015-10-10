<div class="page-header">
    <h2><?= t('New task') ?></h2>
</div>
<form method="post" action="<?= $this->url->href('gantt', 'saveTask', array('project_id' => $values['project_id'])) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('project_id', $values) ?>
    <?= $this->form->hidden('column_id', $values) ?>
    <?= $this->form->hidden('position', $values) ?>

    <div class="form-column">
        <?= $this->form->label(t('Title'), 'title') ?>
        <?= $this->form->text('title', $values, $errors, array('autofocus', 'required', 'maxlength="200"', 'tabindex="1"'), 'form-input-large') ?>

        <?= $this->form->label(t('Description'), 'description') ?>
        <div class="form-tabs">
            <div class="write-area">
                <?= $this->form->textarea('description', $values, $errors, array('placeholder="'.t('Leave a description').'"', 'tabindex="2"')) ?>
            </div>
            <div class="preview-area">
                <div class="markdown"></div>
            </div>
            <ul class="form-tabs-nav">
                <li class="form-tab form-tab-selected">
                    <i class="fa fa-pencil-square-o fa-fw"></i><a id="markdown-write" href="#"><?= t('Write') ?></a>
                </li>
                <li class="form-tab">
                    <a id="markdown-preview" href="#"><i class="fa fa-eye fa-fw"></i><?= t('Preview') ?></a>
                </li>
            </ul>
        </div>

        <?= $this->render('task/color_picker', array('colors_list' => $colors_list, 'values' => $values)) ?>
    </div>

    <div class="form-column">
        <?= $this->form->label(t('Assignee'), 'owner_id') ?>
        <?= $this->form->select('owner_id', $users_list, $values, $errors, array('tabindex="3"')) ?><br/>

        <?= $this->form->label(t('Category'), 'category_id') ?>
        <?= $this->form->select('category_id', $categories_list, $values, $errors, array('tabindex="4"')) ?><br/>

        <?php if (! (count($swimlanes_list) === 1 && key($swimlanes_list) === 0)): ?>
            <?= $this->form->label(t('Swimlane'), 'swimlane_id') ?>
            <?= $this->form->select('swimlane_id', $swimlanes_list, $values, $errors, array('tabindex="5"')) ?><br/>
        <?php endif ?>

        <?= $this->form->label(t('Complexity'), 'score') ?>
        <?= $this->form->number('score', $values, $errors, array('tabindex="6"')) ?><br/>

        <?= $this->form->label(t('Start Date'), 'date_started') ?>
        <?= $this->form->text('date_started', $values, $errors, array('placeholder="'.$this->text->in($date_format, $date_formats).'"', 'tabindex="7"'), 'form-date') ?>

        <?= $this->form->label(t('Due Date'), 'date_due') ?>
        <?= $this->form->text('date_due', $values, $errors, array('placeholder="'.$this->text->in($date_format, $date_formats).'"', 'tabindex="8"'), 'form-date') ?><br/>
        <div class="form-help"><?= t('Others formats accepted: %s and %s', date('Y-m-d'), date('Y_m_d')) ?></div>
    </div>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue" tabindex="11"/>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'board', 'show', array('project_id' => $values['project_id']), false, 'close-popover') ?>
    </div>
</form>
