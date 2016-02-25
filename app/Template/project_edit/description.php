<div class="page-header">
    <h2><?= t('Edit project') ?></h2>
    <ul>
        <li><?= $this->url->link(t('General'), 'ProjectEdit', 'edit', array('project_id' => $project['id'])) ?></li>
        <li><?= $this->url->link(t('Dates'), 'ProjectEdit', 'dates', array('project_id' => $project['id'])) ?></li>
        <li class="active"><?= $this->url->link(t('Description'), 'ProjectEdit', 'description', array('project_id' => $project['id'])) ?></li>
        <li><?= $this->url->link(t('Task priority'), 'ProjectEdit', 'priority', array('project_id' => $project['id'])) ?></li>
    </ul>
</div>
<form method="post" action="<?= $this->url->href('ProjectEdit', 'update', array('project_id' => $project['id'], 'redirect' => 'description')) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('id', $values) ?>
    <?= $this->form->hidden('name', $values) ?>

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
