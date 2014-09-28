<div class="page-header">
    <h2><?= t('Automatic actions for the project "%s"', $project['name']) ?></h2>
</div>

<h3><?= t('Define action parameters') ?></h3>
<form method="post" action="?controller=action&amp;action=create&amp;project_id=<?= $project['id'] ?>" autocomplete="off">
    <?= Helper\form_csrf() ?>
    <?= Helper\form_hidden('project_id', $values) ?>
    <?= Helper\form_hidden('event_name', $values) ?>
    <?= Helper\form_hidden('action_name', $values) ?>

    <?php foreach ($action_params as $param_name => $param_desc): ?>

        <?php if (Helper\contains($param_name, 'column_id')): ?>
            <?= Helper\form_label($param_desc, $param_name) ?>
            <?= Helper\form_select('params['.$param_name.']', $columns_list, $values) ?><br/>
        <?php elseif (Helper\contains($param_name, 'user_id')): ?>
            <?= Helper\form_label($param_desc, $param_name) ?>
            <?= Helper\form_select('params['.$param_name.']', $users_list, $values) ?><br/>
        <?php elseif (Helper\contains($param_name, 'project_id')): ?>
            <?= Helper\form_label($param_desc, $param_name) ?>
            <?= Helper\form_select('params['.$param_name.']', $projects_list, $values) ?><br/>
        <?php elseif (Helper\contains($param_name, 'color_id')): ?>
            <?= Helper\form_label($param_desc, $param_name) ?>
            <?= Helper\form_select('params['.$param_name.']', $colors_list, $values) ?><br/>
        <?php elseif (Helper\contains($param_name, 'category_id')): ?>
            <?= Helper\form_label($param_desc, $param_name) ?>
            <?= Helper\form_select('params['.$param_name.']', $categories_list, $values) ?><br/>
        <?php elseif (Helper\contains($param_name, 'label')): ?>
            <?= Helper\form_label($param_desc, $param_name) ?>
            <?= Helper\form_text('params['.$param_name.']', $values) ?>
        <?php endif ?>

    <?php endforeach ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save this action') ?>" class="btn btn-blue"/>
        <?= t('or') ?> <a href="?controller=action&amp;action=index&amp;project_id=<?= $project['id'] ?>"><?= t('cancel') ?></a>
    </div>
</form>