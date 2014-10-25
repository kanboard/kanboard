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
                    <a href="?controller=category&amp;action=edit&amp;project_id=<?= $project['id'] ?>&amp;category_id=<?= $category_id ?>"><?= t('Edit') ?></a>
                </li>
                <li>
                    <a href="?controller=category&amp;action=confirm&amp;project_id=<?= $project['id'] ?>&amp;category_id=<?= $category_id ?>"><?= t('Remove') ?></a>
                </li>
            </ul>
        </td>
    </tr>
    <?php endforeach ?>
</table>
<?php endif ?>

<h3><?= t('Add a new category') ?></h3>
<form method="post" action="?controller=category&amp;action=save&amp;project_id=<?= $project['id'] ?>" autocomplete="off">

    <?= Helper\form_csrf() ?>
    <?= Helper\form_hidden('project_id', $values) ?>

    <?= Helper\form_label(t('Category Name'), 'name') ?>
    <?= Helper\form_text('name', $values, $errors, array('autofocus required')) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>