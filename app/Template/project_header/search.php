<form method="get" action="<?= $this->url->dir() ?>" class="search">
    <?= $this->form->hidden('controller', $filters) ?>
    <?= $this->form->hidden('action', $filters) ?>
    <?= $this->form->hidden('project_id', $filters) ?>
    <?= $this->form->text('search', $filters, array(), array('placeholder="'.t('Filter').'"'), 'form-input-large') ?>
</form>

<div class="filter-dropdowns">
    <?= $this->render('app/filters_helper', array('reset' => 'status:open', 'project' => $project)) ?>

    <?php if (isset($custom_filters_list) && ! empty($custom_filters_list)): ?>
        <div class="dropdown filters">
        <i class="fa fa-caret-down"></i> <a href="#" class="dropdown-menu"><?= t('My filters') ?></a>
        <ul>
            <?php foreach ($custom_filters_list as $filter): ?>
                <li><a href="#" class="filter-helper" data-<?php if ($filter['append']): ?><?= 'append-' ?><?php endif ?>filter='<?= $this->e($filter['filter']) ?>'><?= $this->e($filter['name']) ?></a></li>
            <?php endforeach ?>
        </ul>
        </div>
    <?php endif ?>

    <?php if (isset($users_list)): ?>
        <div class="dropdown filters">
        <i class="fa fa-caret-down"></i> <a href="#" class="dropdown-menu"><?= t('Users') ?></a>
        <ul>
            <li><a href="#" class="filter-helper" data-append-filter="assignee:nobody"><?= t('Not assigned') ?></a></li>
            <?php foreach ($users_list as $user): ?>
                <li><a href="#" class="filter-helper" data-append-filter='assignee:"<?= $this->e($user) ?>"'><?= $this->e($user) ?></a></li>
            <?php endforeach ?>
        </ul>
        </div>
    <?php endif ?>

    <?php if (isset($categories_list) && ! empty($categories_list)): ?>
        <div class="dropdown filters">
        <i class="fa fa-caret-down"></i> <a href="#" class="dropdown-menu"><?= t('Categories') ?></a>
        <ul>
            <li><a href="#" class="filter-helper" data-append-filter="category:none"><?= t('No category') ?></a></li>
            <?php foreach ($categories_list as $category): ?>
                <li><a href="#" class="filter-helper" data-append-filter='category:"<?= $this->e($category) ?>"'><?= $this->e($category) ?></a></li>
            <?php endforeach ?>
        </ul>
        </div>
    <?php endif ?>
</div>
