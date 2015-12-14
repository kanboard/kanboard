<div class="dropdown filters">
    <i class="fa fa-caret-down"></i> <a href="#" class="dropdown-menu"><?= t('Filters') ?></a>
    <ul>
        <li><a href="#" class="filter-helper filter-reset" data-filter="<?= isset($reset) ? $reset : '' ?>" title="<?= t('Keyboard shortcut: "%s"', 'r') ?>"><?= t('Reset filters') ?></a></li>
        <li><a href="#" class="filter-helper" data-filter="assignee:me"><?= t('My tasks') ?></a></li>
        <li><a href="#" class="filter-helper" data-filter="created:<=<?= date('Y-m-d', time() - 60 * 60 * 24)?>"><?= t('Activity untill yesterday') ?></a></li>
        <li><a href="#" class="filter-helper" data-filter="created:<=<?= date('Y-m-d')?>"><?= t('Activity untill today') ?></a></li>
        <li><a href="#" class="filter-helper" data-filter='project:"Project name"'><?= t('Project') ?></a></li>
        <li><a href="#" class="filter-helper" data-filter="status:closed"><?= t('Closed tasks') ?></a></li>
        <li><a href="#" class="filter-helper" data-filter="status:open"><?= t('Open tasks') ?></a></li>
        <li>
            <?= $this->url->doc(t('View advanced search syntax'), 'search') ?>
        </li>
    </ul>
</div>