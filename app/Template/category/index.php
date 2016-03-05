<?php if (! empty($categories)): ?>
<div class="page-header">
    <h2><?= t('Categories') ?></h2>
</div>
<table>
    <tr>
        <th><?= t('Category Name') ?></th>
        <th class="column-8"><?= t('Actions') ?></th>
    </tr>
    <?php foreach ($categories as $category_id => $category_name): ?>
    <tr>
        <td><?= $this->text->e($category_name) ?></td>
        <td>
            <div class="dropdown">
            <a href="#" class="dropdown-menu dropdown-menu-link-icon"><i class="fa fa-cog fa-fw"></i><i class="fa fa-caret-down"></i></a>
            <ul>
                <li>
                    <?= $this->url->link(t('Edit'), 'category', 'edit', array('project_id' => $project['id'], 'category_id' => $category_id), false, 'popover') ?>
                </li>
                <li>
                    <?= $this->url->link(t('Remove'), 'category', 'confirm', array('project_id' => $project['id'], 'category_id' => $category_id), false, 'popover') ?>
                </li>
            </ul>
            </div>
        </td>
    </tr>
    <?php endforeach ?>
</table>
<?php endif ?>

<div class="page-header">
    <h2><?= t('Add a new category') ?></h2>
</div>
<form method="post" action="<?= $this->url->href('category', 'save', array('project_id' => $project['id'])) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('project_id', $values) ?>

    <?= $this->form->label(t('Category Name'), 'name') ?>
    <?= $this->form->text('name', $values, $errors, array('autofocus', 'required', 'maxlength="50"')) ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-blue"><?= t('Save') ?></button>
    </div>
</form>