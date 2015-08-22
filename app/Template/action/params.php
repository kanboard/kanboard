<div class="page-header">
    <h2><?= t('Automatic actions for the project "%s"', $project['name']) ?></h2>
</div>

<h3><?= t('Define action parameters') ?></h3>
<form method="post" action="<?= $this->url->href('action', 'create', array('project_id' => $project['id'])) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>

    <?= $this->form->hidden('project_id', $values) ?>
    <?= $this->form->hidden('event_name', $values) ?>
    <?= $this->form->hidden('action_name', $values) ?>

    <?php foreach ($action_params as $param_name => $param_desc): ?>

        <?php if ($this->text->contains($param_name, 'column_id')): ?>
            <?= $this->form->label($param_desc, $param_name) ?>
            <?= $this->form->select('params['.$param_name.']', $columns_list, $values) ?><br/>
        <?php elseif ($this->text->contains($param_name, 'user_id')): ?>
            <?= $this->form->label($param_desc, $param_name) ?>
            <?= $this->form->select('params['.$param_name.']', $users_list, $values) ?><br/>
        <?php elseif ($this->text->contains($param_name, 'project_id')): ?>
            <?= $this->form->label($param_desc, $param_name) ?>
            <?= $this->form->select('params['.$param_name.']', $projects_list, $values) ?><br/>
        <?php elseif ($this->text->contains($param_name, 'color_id')): ?>
            <?= $this->form->label($param_desc, $param_name) ?>
            <?= $this->form->select('params['.$param_name.']', $colors_list, $values) ?><br/>
        <?php elseif ($this->text->contains($param_name, 'category_id')): ?>
            <?= $this->form->label($param_desc, $param_name) ?>
            <?= $this->form->select('params['.$param_name.']', $categories_list, $values) ?><br/>
        <?php elseif ($this->text->contains($param_name, 'link_id')): ?>
            <?= $this->form->label($param_desc, $param_name) ?>
            <?= $this->form->select('params['.$param_name.']', $links_list, $values) ?><br/>
        <?php else: ?>
            <?= $this->form->label($param_desc, $param_name) ?>
            <?= $this->form->text('params['.$param_name.']', $values) ?>
        <?php endif ?>

    <?php endforeach ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save this action') ?>" class="btn btn-blue"/>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'action', 'index', array('project_id' => $project['id'])) ?>
    </div>
</form>