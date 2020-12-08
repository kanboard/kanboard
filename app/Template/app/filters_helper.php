<?= $this->hook->render('template:app:filters-helper:before', isset($project) ? array('project' => $project) : array()) ?>
<div class="dropdown">
    <a href="#" class="dropdown-menu dropdown-menu-link-icon" title="<?= t('Default filters') ?>" aria-label="<?= t('Default filters') ?>"><i class="fa fa-filter fa-fw"></i><i class="fa fa-caret-down"></i></a>
    <ul>
        <li><a href="#" class="filter-helper filter-reset" data-filter="<?= isset($reset) ? $reset : '' ?>" title="<?= t('Keyboard shortcut: "%s"', 'r') ?>"><?= t('Reset filters') ?></a></li>
        <li><a href="#" class="filter-helper" data-filter="status:open assignee:me"><?= t('My tasks') ?></a></li>
        <li><a href="#" class="filter-helper" data-filter="status:open assignee:me due:tomorrow"><?= t('My tasks due tomorrow') ?></a></li>
        <li><a href="#" class="filter-helper" data-filter="status:open due:today"><?= t('Tasks due today') ?></a></li>
        <li><a href="#" class="filter-helper" data-filter="status:open due:tomorrow"><?= t('Tasks due tomorrow') ?></a></li>
        <li><a href="#" class="filter-helper" data-filter="status:open due:yesterday"><?= t('Tasks due yesterday') ?></a></li>
        <li><a href="#" class="filter-helper" data-filter="status:closed"><?= t('Closed tasks') ?></a></li>
        <li><a href="#" class="filter-helper" data-filter="status:open"><?= t('Open tasks') ?></a></li>
        <li><a href="#" class="filter-helper" data-filter="status:open assignee:nobody"><?= t('Not assigned') ?></a></li>
        <li><a href="#" class="filter-helper" data-filter="status:open assignee:anybody"><?= t('Assigned') ?></a></li>
        <li><a href="#" class="filter-helper" data-filter="status:open category:none"><?= t('No category') ?></a></li>
        <li>
            <?= $this->url->doc(t('View advanced search syntax'), 'search') ?>
        </li>
    </ul>
</div>
<?= $this->hook->render('template:app:filters-helper:after', isset($project) ? array('project' => $project) : array()) ?>