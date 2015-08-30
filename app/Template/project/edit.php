<div class="page-header">
    <h2><?= t('Edit project') ?></h2>
</div>
<form method="post" action="<?= $this->url->href('project', 'update', array('project_id' => $project['id'])) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('id', $values) ?>

    <?= $this->form->label(t('Name'), 'name') ?>
    <?= $this->form->text('name', $values, $errors, array('required', 'maxlength="50"')) ?>

    <?= $this->form->label(t('Identifier'), 'identifier') ?>
    <?= $this->form->text('identifier', $values, $errors, array('maxlength="50"')) ?>
    <p class="form-help"><?= t('The project identifier is an optional alphanumeric code used to identify your project.') ?></p>

    <?= $this->form->label(t('Start date'), 'start_date') ?>
    <?= $this->form->text('start_date', $values, $errors, array('maxlength="10"'), 'form-date') ?>

    <?= $this->form->label(t('End date'), 'end_date') ?>
    <?= $this->form->text('end_date', $values, $errors, array('maxlength="10"'), 'form-date') ?>

    <?php if ($this->user->isAdmin() || $this->user->isProjectAdministrationAllowed($project['id'])): ?>
        <?= $this->form->checkbox('is_private', t('Private project'), 1, $project['is_private'] == 1) ?>
    <?php endif ?>

    <?= $this->form->label(t('Description'), 'description') ?>

    <div class="form-tabs">

        <div class="write-area">
          <?= $this->form->textarea('description', $values, $errors) ?>
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
    <div class="form-help"><?= $this->url->doc(t('Write your text in Markdown'), 'syntax-guide') ?></div>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>
