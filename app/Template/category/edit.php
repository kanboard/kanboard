<div class="page-header">
    <h2><?= t('Category modification for the project "%s"', $project['name']) ?></h2>
</div>

<form method="post" action="<?= $this->u('category', 'update', array('project_id' => $project['id'], 'category_id' => $values['id'])) ?>" autocomplete="off">

    <?= $this->formCsrf() ?>

    <?= $this->formHidden('id', $values) ?>
    <?= $this->formHidden('project_id', $values) ?>

    <?= $this->formLabel(t('Category Name'), 'name') ?>
    <?= $this->formText('name', $values, $errors, array('autofocus', 'required', 'maxlength="50"')) ?>

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