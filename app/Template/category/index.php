<?php if (! empty($categories)): ?>
<div class="page-header">
    <h2><?= t('Categories') ?></h2>
</div>
<table>
    <tr>
        <th><?= t('Category Name') ?></th>
        <th><?= t('Actions') ?></th>
    </tr>
    <?php foreach ($categories as $category_id => $category_name): ?>
    <tr>
        <td><?= $this->e($category_name) ?></td>
        <td>
            <ul>
                <li>
                    <?= $this->a(t('Edit'), 'category', 'edit', array('project_id' => $project['id'], 'category_id' => $category_id)) ?>
                </li>
                <li>
                    <?= $this->a(t('Remove'), 'category', 'confirm', array('project_id' => $project['id'], 'category_id' => $category_id)) ?>
                </li>
            </ul>
        </td>
    </tr>
    <?php endforeach ?>
</table>
<?php endif ?>

<div class="page-header">
    <h2><?= t('Add a new category') ?></h2>
</div>
<form method="post" action="<?= $this->u('category', 'save', array('project_id' => $project['id'])) ?>" autocomplete="off">

    <?= $this->formCsrf() ?>
    <?= $this->formHidden('project_id', $values) ?>

    <?= $this->formLabel(t('Category Name'), 'name') ?>
    <?= $this->formText('name', $values, $errors, array('autofocus', 'required', 'maxlength="50"')) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>