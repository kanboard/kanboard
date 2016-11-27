<?php if (! empty($categories)): ?>
<div class="page-header">
    <h2><?= t('Categories') ?></h2>
</div>
<table class="table-striped">
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
                    <i class="fa fa-pencil-square-o fa-fw" aria-hidden="true"></i>
                    <?= $this->url->link(t('Edit'), 'CategoryController', 'edit', array('project_id' => $project['id'], 'category_id' => $category_id), false, 'popover') ?>
                </li>
                <li>
                    <i class="fa fa-trash-o fa-fw" aria-hidden="true"></i>
                    <?= $this->url->link(t('Remove'), 'CategoryController', 'confirm', array('project_id' => $project['id'], 'category_id' => $category_id), false, 'popover') ?>
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
<form method="post" action="<?= $this->url->href('CategoryController', 'save', array('project_id' => $project['id'])) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('project_id', $values) ?>

    <?= $this->form->label(t('Category Name'), 'name') ?>
    <?= $this->form->text('name', $values, $errors, array('autofocus', 'required', 'maxlength="50"')) ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-blue"><?= t('Save') ?></button>
    </div>
</form>
