<div class="page-header">
    <h2><?= t('Categories') ?></h2>
</div>

<?php if (! empty($categories)): ?>
<table>
    <tr>
        <th><?= t('Category Name') ?></th>
        <th><?= t('Actions') ?></th>
    </tr>
    <?php foreach ($categories as $category_id => $category_name): ?>
    <tr>
        <td><?= Helper\escape($category_name) ?></td>
        <td>
            <ul>
                <li>
                    <?= Helper\a(t('Edit'), 'category', 'edit', array('project_id' => $project['id'], 'category_id' => $category_id)) ?>
                </li>
                <li>
                    <?= Helper\a(t('Remove'), 'category', 'confirm', array('project_id' => $project['id'], 'category_id' => $category_id)) ?>
                </li>
            </ul>
        </td>
    </tr>
    <?php endforeach ?>
</table>
<?php endif ?>

<h3><?= t('Add a new category') ?></h3>
<form method="post" action="<?= Helper\u('category', 'save', array('project_id' => $project['id'])) ?>" autocomplete="off">

    <?= Helper\form_csrf() ?>
    <?= Helper\form_hidden('project_id', $values) ?>

    <?= Helper\form_label(t('Category Name'), 'name') ?>
    <?= Helper\form_text('name', $values, $errors, array('autofocus required')) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>