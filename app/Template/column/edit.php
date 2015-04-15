<div class="page-header">
    <h2><?= t('Edit column "%s"', $column['title']) ?></h2>
</div>

<form method="post" action="<?= $this->u('column', 'update', array('project_id' => $project['id'], 'column_id' => $column['id'])) ?>" autocomplete="off">

    <?= $this->formCsrf() ?>

    <?= $this->formHidden('id', $values) ?>
    <?= $this->formHidden('project_id', $values) ?>

    <?= $this->formLabel(t('Title'), 'title') ?>
    <?= $this->formText('title', $values, $errors, array('autofocus', 'required', 'maxlength="50"')) ?>

    <?= $this->formLabel(t('Task limit'), 'task_limit') ?>
    <?= $this->formNumber('task_limit', $values, $errors) ?>

    <?= $this->formLabel(t('Description'), 'description') ?>

    <div class="form-tabs">

        <div class="write-area">
          <?= $this->formTextarea('description', $values, $errors) ?>
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
    <div class="form-help"><a href="http://kanboard.net/documentation/syntax-guide" target="_blank" rel="noreferrer"><?= t('Write your text in Markdown') ?></a></div>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>